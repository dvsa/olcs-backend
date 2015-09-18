<?php

/**
 * Create Separator Sheet
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Scan;

use Dvsa\Olcs\Api\Domain\Command\Document\GenerateAndStore;
use Dvsa\Olcs\Api\Domain\Command\PrintScheduler\Enqueue;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity\PrintScan\Scan;
use Dvsa\Olcs\Api\Entity\System\SubCategory;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Exception\RuntimeException;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Api\Entity\Tm\TransportManager;

/**
 * Create Separator Sheet
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
        'Category',
        'SubCategory',
        'SubCategoryDescription'
    ];

    /**
     * @param \Dvsa\Olcs\Transfer\Command\Scan\CreateSeparatorSheet $command
     */
    public function handleCommand(CommandInterface $command)
    {
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

        $scan = new Scan();
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

        $identifier = $this->generateDocument($knownValues);

        $this->result->merge(
            $this->handleSideEffect(
                Enqueue::create(
                    [
                        'fileIdentifier' => $identifier,
                        'jobName' => 'Scanning Separator Sheet',
                    ]
                )
            )
        );

        $this->result->addId('scan', $scan->getId());
        $this->result->addMessage('Scan ID ' . $scan->getId() . ' created');

        return $this->result;
    }

    /**
     * Set the applicable scan properties for a category
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
                return $this->getRepo('Licence')->fetchByLicNo($entityIdentifier);
            case Category::CATEGORY_COMPLIANCE:
                return $this->getRepo('Cases')->fetchById($entityIdentifier);
            case Category::CATEGORY_IRFO:
                return $this->getRepo()->getReference(Organisation::class, $entityIdentifier);
            case Category::CATEGORY_TRANSPORT_MANAGER:
                return $this->getRepo()->getReference(TransportManager::class, $entityIdentifier);
            case Category::CATEGORY_BUS_REGISTRATION:
                /* @var $busRegSearch \Dvsa\Olcs\Api\Entity\View\BusRegSearchView */
                $busRegSearch = $this->getRepo('BusRegSearchView')->fetchByRegNo($entityIdentifier);
                return $this->getRepo('Bus')->fetchById($busRegSearch->getId());
            default:
                throw new RuntimeException('Cannot get an entity for category Id ' . $categoryId);
        }
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
            throw new RuntimeException('Entity name is not setup for category Id ' . $categoryId);
        }

        return $names[$categoryId];
    }

    /**
     * Get the Lic No associated with the entity or "Unknown" if no Lic No is associated
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

    protected function generateDocument($knownValues)
    {
        $dtoData = [
            'template' => 'Scanning_SeparatorSheet',
            'query' => [],
            'knownValues' => $knownValues,
            'description' => 'Scanning separator',
            'category' => Category::CATEGORY_LICENSING,
            'subCategory' => SubCategory::DOC_SUB_CATEGORY_SCANNING_SEPARATOR,
            'isExternal' => false,
            'isScan' => false
        ];

        $result = $this->handleSideEffect(GenerateAndStore::create($dtoData));

        $this->result->merge($result);

        return $result->getId('identifier');
    }
}
