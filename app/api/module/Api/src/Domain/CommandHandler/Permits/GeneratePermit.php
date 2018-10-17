<?php

/**
 * Generate Permit
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\Command\Document\GenerateAndStore;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\UploaderAwareInterface;
use Dvsa\Olcs\Api\Entity\System\Category as CategoryEntity;
use Dvsa\Olcs\Api\Entity\System\SubCategory as SubCategoryEntity;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\UploaderAwareTrait;

/**
 * Generate a Permit in RTF form
 *
 * @author Henry White <henry.white@capgemini.com>
 */
final class GeneratePermit extends AbstractCommandHandler implements UploaderAwareInterface
{
    use UploaderAwareTrait;

    protected $repoServiceName = 'IrhpPermit';

    protected $extraRepos = ['IrhpPermitApplication'];

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        foreach ($command->getIds() as $id) {
            $irhpPermit = $this->getRepo()->fetchById($id, Query::HYDRATE_OBJECT);

            $irhpPermitApplication = $irhpPermit->getIrhpPermitApplication();
            $irhpPermitStock = $irhpPermitApplication
                ->getIrhpPermitWindow()
                ->getIrhpPermitStock();

            $irhpPermitType = $irhpPermitStock
                ->getIrhpPermitType();

            $description = sprintf(
                'Permit %d',
                $irhpPermit->getId()
            );

            $document = $this->handleSideEffect(
                GenerateAndStore::create(
                    [
                        'template' => 'IRHP_'. strtoupper($irhpPermitType->getName()),
                        'query' => [
                            'licence' => $irhpPermitApplication->getLicence()->getId(),
                            'irhpPermit' => $irhpPermit->getId(),
                            'irhpPermitStock' => $irhpPermitStock->getId(),
                            'organisation' => $irhpPermitApplication->getLicence()->getOrganisation()->getId()
                        ],
                        'knownValues' => [],
                        'description' => $description,
                        'category' => CategoryEntity::CATEGORY_PERMITS,
                        'subCategory' => SubCategoryEntity::DOC_SUB_CATEGORY_CONTINUATIONS_AND_RENEWALS_LICENCE,
                        'isExternal' => false,
                        'isScan' => false
                    ]
                )
            );

            foreach($document->getMessages() as $message) {
                $result->addMessage($message);
            }
        }

        return $result;
    }
}
