<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Irfo;

use Dvsa\Olcs\Api\Domain\Command\Document\GenerateAndStore;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity\System\Category as CategoryEntity;
use Dvsa\Olcs\Api\Entity\System\SubCategory as SubCategoryEntity;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Generate IrfoPsvAuth
 */
final class GenerateIrfoPsvAuth extends AbstractCommandHandler implements TransactionedInterface
{
    use IrfoPsvAuthUpdateTrait;

    protected $repoServiceName = 'IrfoPsvAuth';

    protected $extraRepos = ['Fee'];

    /**
     * Handle Generate command
     *
     * @param CommandInterface $command
     * @return Result
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    public function handleCommand(CommandInterface $command)
    {
        // common IRFO PSV Auth update
        $irfoPsvAuth = $this->updateIrfoPsvAuth($command);

        $irfoPsvAuth->generate(
            $this->getRepo('Fee')->fetchFeesByIrfoPsvAuthId($irfoPsvAuth->getId(), true)
        );

        $this->getRepo()->save($irfoPsvAuth);

        $template = 'IRFO_PSV_'
            . str_replace(' ', '_', $irfoPsvAuth->getIrfoPsvAuthType()->getSectionCode());

        $description = sprintf(
            'IRFO PSV Authorisation (%d) x %d',
            $irfoPsvAuth->getId(),
            $command->getCopiesRequiredTotal()
        );

        // generate document
        $this->handleSideEffect(
            GenerateAndStore::create(
                [
                    'template' => $template,
                    'query' => [
                        'irfoPsvAuth' => $irfoPsvAuth->getId(),
                        'organisation' => $irfoPsvAuth->getOrganisation()->getId(),
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

        $result = new Result();
        $result->addId('irfoPsvAuth', $irfoPsvAuth->getId());
        $result->addMessage('IRFO PSV Auth generated successfully');

        return $result;
    }
}
