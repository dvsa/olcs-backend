<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Scan;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Exception\RuntimeException;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Entity\System\Category;

/**
 * CreateSeperatorSheet
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
final class CreateSeparatorSheet extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Scan';

    protected $extraRepos = [
        'Licence',
        'Bus',
        'BusRegSearchView',
        'Cases',
        'Licence',
        'Licence',
        'Organisation',
        'TransportManager',
        'Category',
        'SubCategory',
        'SubCategoryDescription'
    ];


    public function handleCommand(CommandInterface $command)
    {
        /* @var $command \Dvsa\Olcs\Transfer\Command\Scan\CreateSeperatorSheet */

        if (empty($command->getDescription()) && empty($command->getDescriptionId())) {
            throw new ValidationException(['description or descriptionId must be specified']);
        }

        $entity = $this->getEntityForCategory($command->getCategoryId(), $command->getEntityIdentifier());

        if ($command->getDescriptionId()) {
            $descriptionName = $this->getRepo('SubCategoryDescription')
                ->fetchById($command->getDescriptionId())
                ->getDescription();
        } else {
            $descriptionName = $command->getDescription();
        }

        $scan = new \Dvsa\Olcs\Api\Entity\PrintScan\Scan();
        $scan->setCategory($this->getRepo()->getCategoryReference($command->getCategoryId()));
        $scan->setSubCategory($this->getRepo()->getSubCategoryReference($command->getSubCategoryId()));
        $scan->setDescription($descriptionName);

        $this->setScanProperties($command->getCategoryId(), $scan, $entity);

        $this->getRepo()->save($scan);

        $knownValues = [
            'DOC_CATEGORY_ID_SCAN'       => $command->getCategoryId(),
            'DOC_CATEGORY_NAME_SCAN'     => $this->getRepo('Category')->fetchById($command->getCategoryId())
                ->getDescription(),
            'LICENCE_NUMBER_SCAN'        => $this->getLicNo($entity),
            'LICENCE_NUMBER_REPEAT_SCAN' => $this->getLicNo($entity),
            'ENTITY_ID_TYPE_SCAN'        => $this->getEntityTypeForCategory($command->getCategoryId()),
            'ENTITY_ID_SCAN'             => $entity->getId(),
            'ENTITY_ID_REPEAT_SCAN'      => $entity->getId(),
            'DOC_SUBCATEGORY_ID_SCAN'    => $command->getSubCategoryId(),
            'DOC_SUBCATEGORY_NAME_SCAN'  => $this->getRepo('SubCategory')->fetchById($command->getSubCategoryId())
                ->getSubCategoryName(),
            'DOC_DESCRIPTION_ID_SCAN'    => $scan->getId(),
            'DOC_DESCRIPTION_NAME_SCAN'  => $descriptionName,
        ];

        // @todo Integrate with doc generation when its merged in

//        $docService = $this->getDocumentGenerator();
//        $content = $docService->generateFromTemplate('Scanning_SeparatorSheet', [], $knownValues);
//        $storedFile = $docService->uploadGeneratedContent($content, 'documents', 'Scanning Separator Sheet');

//        $this->handleCommand(
//            \Dvsa\Olcs\Api\Domain\Command\PrintScheduler\Enqueue::create(
//                [
//                    'fileIdentifier' => $storedFile->getIdentifier(),
//                    'jobName' => 'Scanning Separator Sheet',
//                ]
//            )
//        );

        $result = new Result();
        $result->addId('scan', $scan->getId());
        $result->addMessage("Scan ID {$scan->getId()} created");

        return $result;
    }

    /**
     * Set the applicabale scan properties for a category
     *
     * @param int $categoryId
     * @param \Dvsa\Olcs\Api\Entity\PrintScan\Scan $scan
     * @param object $entity
     */
    protected function setScanProperties($categoryId, $scan, $entity)
    {
        // set associated entities dependent on category
        switch ($categoryId) {
            case Category::CATEGORY_APPLICATION:
            case Category::CATEGORY_LICENSING:
            case Category::CATEGORY_ENVIRONMENTAL:
                $scan->setLicence($entity);
                break;
            case Category::CATEGORY_COMPLIANCE:
                $scan->setCase($entity);
                $scan->setLicence($entity->getLicence());
                $scan->setTransportManager($entity->getTransportManager());
                break;
            case Category::CATEGORY_IRFO:
                $scan->setIrfoOrganisation($entity);
                break;
            case Category::CATEGORY_TRANSPORT_MANAGER:
                $scan->setTransportManager($entity);
                break;
            case Category::CATEGORY_BUS_REGISTRATION:
                $scan->setBusReg($entity);
                $scan->setLicence($entity->getLicence());
                break;
        }
    }

    /**
     * Get the entity that a category is related to
     *
     * @param int $categoryId
     *
     * @return An entity
     * @throws RuntimeException
     */
    protected function getEntityForCategory($categoryId, $entityIdentifier)
    {
        switch ($categoryId) {
            case Category::CATEGORY_APPLICATION:
            case Category::CATEGORY_LICENSING:
            case Category::CATEGORY_ENVIRONMENTAL:
                $entity = $this->getRepo('Licence')->fetchByLicNo($entityIdentifier);
                break;
            case Category::CATEGORY_BUS_REGISTRATION:
                /* @var $busRegSearch \Dvsa\Olcs\Api\Entity\View\BusRegSearchView */
                $busRegSearch = $this->getRepo('BusRegSearchView')->fetchByRegNo($entityIdentifier);
                $entity = $this->getRepo('Bus')->fetchById($busRegSearch->getId());
                break;
            case Category::CATEGORY_COMPLIANCE:
                $entity = $this->getRepo('Cases')->fetchById($entityIdentifier);
                break;
            case Category::CATEGORY_IRFO:
                $entity = $this->getRepo('Organisation')->fetchById($entityIdentifier);
                break;
            case Category::CATEGORY_TRANSPORT_MANAGER:
                $entity = $this->getRepo('TransportManager')->fetchById($entityIdentifier);
                break;
            default:
                throw new RuntimeException("Cannot get an entity for category Id {$categoryId}");
        };

        return $entity;
    }

    /**
     * Get the Entity Name for a category ID
     *
     * @param int $categoryId
     *
     * @return string
     * @throws RuntimeException
     */
    protected function getEntityTypeForCategory($categoryId)
    {
        $names = [
            Category::CATEGORY_APPLICATION       => 'Licence',
            Category::CATEGORY_BUS_REGISTRATION  => 'Bus Route',
            Category::CATEGORY_COMPLIANCE        => 'Case',
            Category::CATEGORY_LICENSING         => 'Licence',
            Category::CATEGORY_ENVIRONMENTAL     => 'Licence',
            Category::CATEGORY_IRFO              => 'IRFO',
            Category::CATEGORY_TRANSPORT_MANAGER => 'Transport Manager'
        ];

        if (!isset($names[$categoryId])) {
            throw new RuntimeException("Entity name is not setup for category Id {$categoryId}");
        }

        return $names[$categoryId];
    }

    /**
     * Get the Lic No associated with the entity 0r "Unknown" of no Lic No is associated
     *
     * @param $entity
     *
     * @return string Lic No or "Unknown"
     */
    protected function getLicNo($entity)
    {
        if (method_exists($entity, 'getLicNo')) {
            return $entity->getLicNo();
        }

        if (method_exists($entity, 'getLicence')) {
            return $entity->getLicence()->getLicNo();
        }

        return 'Unknown';
    }
}
