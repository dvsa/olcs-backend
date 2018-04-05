<?php

/**
 * Generate Irfo Gv Permit
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\EcmtPermits;

use Dvsa\Olcs\Api\Domain\Command\Document\GenerateAndStore;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Exception\BadRequestException;
use Dvsa\Olcs\Api\Entity\System\Category as CategoryEntity;
use Dvsa\Olcs\Api\Entity\System\SubCategory as SubCategoryEntity;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Doctrine\ORM\Query;

/**
 * Generate Irfo Gv Permit
 */
final class GenerateEcmtPermit extends AbstractCommandHandler
{
    protected $repoServiceName = 'EcmtPermits';

    public function handleCommand(CommandInterface $command)
    {
        $ecmtPermit = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT);

        $template = 'IRFO_GV_ECMT';

        $description = sprintf(
            'ECMT Permit %d',
            $ecmtPermit->getId()
        );

        // generate document
        $this->handleSideEffect(
            GenerateAndStore::create(
                [
                    'template' => $template,
                    'knownValues' => [
                        'IRFO_GV_START_DATE' => '01/01/2018',
                        'IRFO_GV_END_DATE' => '31/12/2018',
                        'IRFO_TA_NAME' => $ecmtPermit->getEcmtPermitsApplication()->getLicence()->getOrganisation()->getId(),
                    ],
                    'description' => $description,
                    'category' => CategoryEntity::CATEGORY_IRFO,
                    'subCategory' => SubCategoryEntity::DOC_SUB_CATEGORY_IRFO_CONTINUATIONS_AND_RENEWALS,
                    'isExternal' => false,
                    'isScan' => false
                ]
            )
        );

        $result = new Result();
        $result->addMessage(sprintf('ECMT Permit %d RTF generated successfully', $ecmtPermit->getId()));

        return $result;
    }
}
