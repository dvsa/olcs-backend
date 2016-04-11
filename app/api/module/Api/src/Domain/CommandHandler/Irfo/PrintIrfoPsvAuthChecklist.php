<?php

/**
 * Print IRFO Psv Auth Checklist
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Irfo;

use Dvsa\Olcs\Api\Domain\Command\Document\GenerateAndStore;
use Dvsa\Olcs\Api\Domain\Command\PrintScheduler\Enqueue as EnqueueFileCommand;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Exception;
use Dvsa\Olcs\Api\Entity\Irfo\IrfoPsvAuth;
use Dvsa\Olcs\Api\Entity\Irfo\IrfoPsvAuthType;
use Dvsa\Olcs\Api\Entity\System\Category as CategoryEntity;
use Dvsa\Olcs\Api\Entity\System\SubCategory as SubCategoryEntity;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Print IRFO Psv Auth Checklist
 */
final class PrintIrfoPsvAuthChecklist extends AbstractCommandHandler implements TransactionedInterface
{
    const MAX_IDS_COUNT = 100;

    protected $repoServiceName = 'IrfoPsvAuth';

    /**
     * Handle the command
     *
     * @param CommandInterface $command
     *
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        $ids = $command->getIds();

        if (count($ids) > self::MAX_IDS_COUNT) {
            throw new Exception\ValidationException(
                ['Number of selected records must be less than or equal to '.self::MAX_IDS_COUNT]
            );
        }

        $irfoPsvAuthList = $this->getRepo()->fetchByIds($ids);

        $result = new Result();

        foreach ($irfoPsvAuthList as $irfoPsvAuth) {
            $result->merge($this->printChecklist($irfoPsvAuth));
        }

        return $result;
    }

    /**
     * Print IRFO PSV Auth Checklist
     *
     * @param IrfoPsvAuth $irfoPsvAuth
     *
     * @return Result
     */
    private function printChecklist(IrfoPsvAuth $irfoPsvAuth)
    {
        $result = new Result();

        // print renewal letter
        $result->merge($this->printRenewalLetter($irfoPsvAuth));

        // print service letter
        $result->merge($this->printServiceLetter($irfoPsvAuth));

        return $result;
    }

    /**
     * Print Renewal Letter
     *
     * @param IrfoPsvAuth $irfoPsvAuth
     *
     * @return Result
     */
    private function printRenewalLetter(IrfoPsvAuth $irfoPsvAuth)
    {
        $description = sprintf(
            'IRFO PSV Auth Checklist Renewal letter (%d)',
            $irfoPsvAuth->getId()
        );

        // generate document
        $result = $this->handleSideEffect(
            GenerateAndStore::create(
                [
                    'template' => 'IRFO_Checklist_Renewal_letter',
                    'query' => [
                        'irfoPsvAuth' => $irfoPsvAuth->getId()
                    ],
                    'knownValues' => [],
                    'description' => $description,
                    'irfoOrganisation' => $irfoPsvAuth->getOrganisation()->getId(),
                    'category' => CategoryEntity::CATEGORY_IRFO,
                    'subCategory' => SubCategoryEntity::DOC_SUB_CATEGORY_IRFO_CONTINUATIONS_AND_RENEWALS,
                    'isExternal' => false,
                    'isScan' => false
                ]
            )
        );

        // add to the print queue
        return $this->handleSideEffect(
            EnqueueFileCommand::create(
                [
                    'documentId' => $result->getId('document'),
                    'jobName' => $description
                ]
            )
        );
    }

    /**
     * Print Service Letter
     *
     * @param IrfoPsvAuth $irfoPsvAuth
     *
     * @return Result
     */
    private function printServiceLetter(IrfoPsvAuth $irfoPsvAuth)
    {
        $templates = $this->getServiceLetterTemplates($irfoPsvAuth);

        if (empty($templates)) {
            throw new Exception\BadRequestException(
                'No template found for given IRFO PSV Auth Type: '
                .$irfoPsvAuth->getIrfoPsvAuthType()->getIrfoFeeType()->getId()
            );
        }

        $printResult = new Result();

        foreach ($templates as $template) {
            $description = sprintf(
                '%s (%d)',
                str_replace('_', ' ', $template),
                $irfoPsvAuth->getId()
            );

            // generate document
            $result = $this->handleSideEffect(
                GenerateAndStore::create(
                    [
                        'template' => $template,
                        'query' => [
                            'irfoPsvAuth' => $irfoPsvAuth->getId()
                        ],
                        'knownValues' => [],
                        'description' => $description,
                        'irfoOrganisation' => $irfoPsvAuth->getOrganisation()->getId(),
                        'category' => CategoryEntity::CATEGORY_IRFO,
                        'subCategory' => SubCategoryEntity::DOC_SUB_CATEGORY_IRFO_CONTINUATIONS_AND_RENEWALS,
                        'isExternal' => false,
                        'isScan' => false
                    ]
                )
            );

            // add to the print queue
            $printResult->merge(
                $this->handleSideEffect(
                    EnqueueFileCommand::create(
                        [
                            'documentId' => $result->getId('document'),
                            'jobName' => $description
                        ]
                    )
                )
            );
        }

        return $printResult;
    }

    /**
     * Get Service Letter Templates
     *
     * @param IrfoPsvAuth $irfoPsvAuth
     *
     * @return array
     */
    private function getServiceLetterTemplates(IrfoPsvAuth $irfoPsvAuth)
    {
        $templates = [];

        switch ($irfoPsvAuth->getIrfoPsvAuthType()->getIrfoFeeType()->getId()) {
            case IrfoPsvAuthType::IRFO_FEE_TYPE_EU_REG_17:
            case IrfoPsvAuthType::IRFO_FEE_TYPE_EU_REG_19A:
                $templates = ['IRFO_eu_auth_pink_GV280'];
                break;
            case IrfoPsvAuthType::IRFO_FEE_TYPE_NON_EU_REG_18:
                $templates = [
                    'IRFO_uk_green_authorisation_INT_P17',
                    'IRFO_non_eu_blue_authorisation_to_foreign_partner_INT_P18'
                ];
                break;
            case IrfoPsvAuthType::IRFO_FEE_TYPE_NON_EU_REG_19:
                $templates = ['IRFO_non_eu_blue_authorisation_foreign_operator_no_partner_INT_P18A'];
                break;
            case IrfoPsvAuthType::IRFO_FEE_TYPE_NON_EU_OCCASIONAL_19:
            case IrfoPsvAuthType::IRFO_FEE_TYPE_SHUTTLE_OPERATOR_20:
                $templates = ['IRFO_eu_auth_pink_special_regular_GV280'];
                break;
            case IrfoPsvAuthType::IRFO_FEE_TYPE_OWN_AC_21:
                $templates = ['IRFO_own_acc'];
                break;
        }

        return $templates;
    }
}
