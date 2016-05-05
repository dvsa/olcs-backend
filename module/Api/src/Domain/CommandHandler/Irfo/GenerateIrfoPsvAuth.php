<?php

/**
 * Generate IrfoPsvAuth
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Irfo;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Document\GenerateAndStore;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity\Irfo\IrfoPsvAuth;
use Dvsa\Olcs\Api\Entity\System\Category as CategoryEntity;
use Dvsa\Olcs\Api\Entity\System\SubCategory as SubCategoryEntity;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\Irfo\UpdateIrfoPsvAuth as UpdateDto;

/**
 * Generate IrfoPsvAuth
 */
final class GenerateIrfoPsvAuth extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'IrfoPsvAuth';

    protected $extraRepos = ['Fee'];

    /**
     * Generates Irfo Psv Auth
     *
     * @param CommandInterface $command
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        /** @var IrfoPsvAuth $irfoPsvAuth */
        $irfoPsvAuth = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT);

        $this->handleSideEffect(
            UpdateDto::create(
                $command->getArrayCopy()
            )
        );

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
