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
        // generate document
        $result = $this->handleSideEffect(
            GenerateAndStore::create(
                [
                    'template' => 'IRFO_Checklist_Renewal_letter',
                    'query' => [
                        'irfoPsvAuth' => $irfoPsvAuth->getId()
                    ],
                    'knownValues' => [],
                    'description' => 'IRFO PSV Auth Checklist Renewal letter',
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
                    'jobName' => 'IRFO PSV Auth Checklist Renewal letter'
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
        switch ($irfoPsvAuth->getIrfoPsvAuthType()->getIrfoFeeType()->getId()) {
            case IrfoPsvAuthType::IRFO_FEE_TYPE_EU_REG_17:
            case IrfoPsvAuthType::IRFO_FEE_TYPE_EU_REG_19A:
                $template = 'IRFO_app_eu_regular_service';
                break;
            case IrfoPsvAuthType::IRFO_FEE_TYPE_OWN_AC_21:
                $template = 'IRFO_own_acc';
                break;
            default:
                $template = 'IRFO_app_non_eu_service';
                break;
        }

        // generate document
        $result = $this->handleSideEffect(
            GenerateAndStore::create(
                [
                    'template' => $template,
                    'query' => [
                        'irfoPsvAuth' => $irfoPsvAuth->getId()
                    ],
                    'knownValues' => [],
                    'description' => 'IRFO PSV Auth Checklist Service letter',
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
                    'jobName' => 'IRFO PSV Auth Checklist Service letter'
                ]
            )
        );
    }
}
