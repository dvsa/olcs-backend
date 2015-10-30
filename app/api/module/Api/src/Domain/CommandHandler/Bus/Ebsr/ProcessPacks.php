<?php

/**
 * Process Ebsr packs
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Bus\Ebsr;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Domain\Exception;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\Bus\Ebsr\RequestMap as RequestMapQueueCmd;
use Dvsa\Olcs\Api\Service\Ebsr\FileProcessorInterface;
use Dvsa\Olcs\Api\Service\Ebsr\FileProcessor;
use Zend\Filter\Decompress;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation as OrganisationEntity;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Ebsr\EbsrSubmission as EbsrSubmissionEntity;
use Dvsa\Olcs\Api\Entity\Bus\BusReg as BusRegEntity;
use Dvsa\Olcs\Api\Entity\Bus\BusNoticePeriod as BusNoticePeriodEntity;
use Dvsa\Olcs\Api\Entity\Bus\BusServiceType as BusServiceTypeEntity;
use Dvsa\Olcs\Api\Entity\Doc\Document as DocumentEntity;
use Dvsa\Olcs\Api\Entity\System\Category as CategoryEntity;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea as TrafficAreaEntity;
use Dvsa\Olcs\Api\Entity\Bus\LocalAuthority as LocalAuthorityEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Task\Task as TaskEntity;
use Dvsa\Olcs\Transfer\Command\Bus\Ebsr\ProcessPacks as ProcessPacksCmd;
use Dvsa\Olcs\Transfer\Command\Document\Upload as UploadCmd;
use Dvsa\Olcs\Api\Domain\Command\Task\CreateTask as CreateTaskCmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\UploaderAwareInterface;
use Dvsa\Olcs\Api\Domain\UploaderAwareTrait;
use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Zend\ServiceManager\ServiceLocatorInterface;
use Doctrine\ORM\Query;

/**
 * Process Ebsr packs
 * @todo Ian L 29/10/15 - General tidy up. Refine and reorganise (mainly validation), improve error messages
 */
final class ProcessPacks extends AbstractCommandHandler implements
    AuthAwareInterface,
    TransactionedInterface,
    UploaderAwareInterface
{
    use AuthAwareTrait;
    use UploaderAwareTrait;

    protected $repoServiceName = 'bus';

    protected $extraRepos = [
        'Document',
        'EbsrSubmission',
        'Licence',
        'BusRegOtherService',
        'TrafficArea',
        'LocalAuthority',
        'BusServiceType'
    ];

    protected $xmlStructure;

    protected $busRegInput;

    /**
     * @var FileProcessor
     */
    protected $fileProcessor;

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $mainServiceLocator = $serviceLocator->getServiceLocator();

        $this->xmlStructure = $mainServiceLocator->get('EbsrXmlStructure');
        $this->busRegInput = $mainServiceLocator->get('EbsrBusRegInput');
        $this->fileProcessor = $mainServiceLocator->get(FileProcessorInterface::class);

        return parent::createService($serviceLocator);
    }

    /**
     * @param CommandInterface $command
     * @return Result
     * @throws \Exception
     */
    public function handleCommand(CommandInterface $command)
    {
        /** @var ProcessPacksCmd $command */
        $result = new Result();

        $packs = $command->getPacks();
        $organisation = $this->getCurrentOrganisation();

        $validPacks = 0;
        $invalidPacks = 0;

        foreach ($packs as $packId) {
            /** @var DocumentEntity $document */
            $document = $this->getRepo('Document')->fetchById($packId);
            $ebsrSubmission = $this->createEbsrSubmission($organisation, $document, $command->getSubmissionType());
            $this->getRepo('EbsrSubmission')->save($ebsrSubmission);
            $result->addId('ebsrSubmission_' . $ebsrSubmission->getId(), $ebsrSubmission->getId());

            try {
                $xmlFilename = $this->fileProcessor->fetchXmlFileNameFromDocumentStore($document->getIdentifier());
            } catch (\RuntimeException $e) {
                $invalidPacks++;

                $result->addId(
                    'error_messages',
                    'Error with ' . $document->getDescription() . ': ' . $e->getMessage() . ' - not processed',
                    true
                );

                continue;
            }

            $this->xmlStructure->setValue($xmlFilename);

            if (!$this->xmlStructure->isValid(['xml_filename' => $xmlFilename])) {
                //@todo return more specific messages
                $invalidPacks++;

                $result->addId(
                    'error_messages',
                    'Error with ' . $document->getDescription() . '(' . basename($xmlFilename) .
                    '): xml file did not pass validation - not processed',
                    true
                );

                continue;
            }

            $ebsrDoc = $this->xmlStructure->getValue();

            $this->busRegInput->setValue($ebsrDoc);

            if (!$this->busRegInput->isValid(['submissionType' => $command->getSubmissionType()])) {
                //@todo return more specific messages
                $invalidPacks++;

                $result->addId(
                    'error_messages',
                    'Error with ' . $document->getDescription() . '(' . basename($xmlFilename) .
                    '): xml file data did not meet business rules - not processed',
                    true
                );

                continue;
            }

            $ebsrData = $this->busRegInput->getValue();

            $ebsrSubmission->updateStatus(
                $this->getRepo()->getRefdataReference(EbsrSubmissionEntity::VALIDATED_STATUS)
            );

            $ebsrSubmission->setLicenceNo($ebsrData['licNo']);
            $ebsrSubmission->setVariationNo($ebsrData['variationNo']);
            $ebsrSubmission->setRegistrationNo($ebsrData['routeNo']);
            $this->getRepo('EbsrSubmission')->save($ebsrSubmission);

            $ebsrData = $this->processEbsrInformation($ebsrData);

            try {
                $busReg = $this->createBusReg($ebsrData);
            } catch (Exception\NotFoundException $e) {
                $invalidPacks++;
                //@todo make message specific
                $result->addId(
                    'error_messages',
                    'Error with ' . $document->getDescription() . ': ' . strtolower($e->getMessages()[0]) .
                    ' - not processed',
                    true
                );

                continue;
            } catch (Exception\ForbiddenException $e) {
                $invalidPacks++;
                $result->addId(
                    'error_messages',
                    'Error with ' . $document->getDescription() . ': ' . strtolower($e->getMessages()[0]) .
                    ' - not processed',
                    true
                );

                continue;
            }

            $busSubmissions = new ArrayCollection();
            $busSubmissions->add($ebsrSubmission);
            $busReg->setEbsrSubmissions($busSubmissions);

            $this->getRepo()->save($busReg);

            $sideEffects = $this->getSideEffects($ebsrData, $busReg, dirname($xmlFilename));

            $ebsrSubmission->updateStatus(
                $this->getRepo()->getRefdataReference(EbsrSubmissionEntity::PROCESSED_STATUS)
            );

            $ebsrSubmission->setBusReg($busReg);

            $this->getRepo('EbsrSubmission')->save($ebsrSubmission);

            $this->handleSideEffects($sideEffects);
            $validPacks++;

            $result->addMessage(
                $document->getDescription() . '(' . basename($xmlFilename) . '): file processed successfully'
            );
        }

        $result->addId('valid', $validPacks);
        $result->addId('errors', $invalidPacks);

        return $result;
    }

    /**
     * @param OrganisationEntity $organisation
     * @param DocumentEntity $document
     * @param $submissionType
     * @return EbsrSubmissionEntity
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    private function createEbsrSubmission(OrganisationEntity $organisation, DocumentEntity $document, $submissionType)
    {
        return new EbsrSubmissionEntity(
            $organisation,
            $this->getRepo()->getRefdataReference(EbsrSubmissionEntity::VALIDATING_STATUS),
            $this->getRepo()->getRefdataReference($submissionType),
            $document,
            new \DateTime()
        );
    }

    /**
     * @param array $ebsrData
     * @throws Exception\ForbiddenException
     * @return BusRegEntity
     */
    private function createBusReg(array $ebsrData)
    {
        //decide what to do based on txcAppType
        switch ($ebsrData['txcAppType']) {
            case 'new':
                $busReg = $this->createNew($ebsrData);
                break;
            case 'cancel':
                $busReg = $this->createCancel($ebsrData);
                break;
            default:
                $busReg = $this->createVar($ebsrData);
        }

        $busReg->fromData($this->prepareBusRegData($ebsrData));

        $busReg->populateShortNotice();

        if ($busReg->getIsShortNotice() === 'Y') {
            if (empty($ebsrData['busShortNotice'])) {
                throw new Exception\ForbiddenException(
                    'This application is short notice, but the file doesn\'t have a short notice section'
                );
            }

            $busReg->getShortNotice()->createEbsrShortNotice($ebsrData['busShortNotice']);
        }

        $this->processServiceNumbers($busReg, $ebsrData['otherServiceNumbers']);

        return $busReg;
    }

    /**
     * Unset any data keys that might clash with the busReg entity fromData method
     *
     * @param array $ebsrData
     * @return array
     */
    private function prepareBusRegData($ebsrData)
    {
        $busRegData = $ebsrData;
        unset($busRegData['documents']);
        unset($busRegData['variationNo']);
        return $busRegData;
    }

    /**
     * @param array $ebsrData
     * @param BusRegEntity $busReg
     * @return array
     */
    private function getSideEffects(array $ebsrData, BusRegEntity $busReg, $documentPath)
    {
        $sideEffects = $this->persistDocuments($ebsrData, $busReg, $documentPath);
        $sideEffects[] = $this->getRequestMapQueueCmd($busReg->getId());
        $sideEffects[] = $this->createTaskCommand($busReg);

        return $sideEffects;
    }

    /**
     * @param array $ebsrData
     * @param BusRegEntity $busReg
     * @return array
     */
    private function persistDocuments(array $ebsrData, BusRegEntity $busReg, $documentPath)
    {
        $sideEffects = [];

        //store any supporting documents
        if (isset($ebsrData['documents'])) {
            foreach ($ebsrData['documents'] as $documentName) {
                $sideEffects[] = $this->persistSupportingDocument(
                    $documentPath . '/' . $documentName,
                    $busReg,
                    $documentName,
                    'Supporting document'
                );
            }
        }

        //store a new map if present
        if (isset($ebsrData['map'])) {
            $sideEffects[] = $this->persistSupportingDocument(
                $documentPath . '/' . $ebsrData['map'],
                $busReg,
                $ebsrData['map'],
                'Schematic map'
            );
        }

        return $sideEffects;
    }

    /**
     * @param string $content
     * @param BusRegEntity $busReg
     * @param string $filename
     * @param string $description
     * @return UploadCmd
     */
    private function persistSupportingDocument($content, BusRegEntity $busReg, $filename, $description)
    {
        $data = [
            'content' => base64_encode(file_get_contents($content)),
            'busReg' => $busReg->getId(),
            'licence' => $busReg->getLicence()->getId(),
            'category' => CategoryEntity::CATEGORY_BUS_REGISTRATION,
            'subCategory' => CategoryEntity::BUS_SUB_CATEGORY_OTHER_DOCUMENTS,
            'filename' => $filename,
            'description' => $description
        ];

        return UploadCmd::create($data);
    }

    /**
     * @param int $busRegId
     * @return RequestMapQueueCmd
     */
    private function getRequestMapQueueCmd($busRegId)
    {
        return RequestMapQueueCmd::create(['id' => $busRegId, 'scale' => 'small']);
    }

    /**
     * @param BusRegEntity $busReg
     * @return CreateTaskCmd
     */
    private function createTaskCommand(BusRegEntity $busReg)
    {
        $submissionType = $busReg->getEbsrSubmissions()->first()->getEbsrSubmissionType();

        if ($submissionType === EbsrSubmissionEntity::DATA_REFRESH_SUBMISSION_TYPE) {
            $description = 'Data refresh created';
        } else {
            $status = $busReg->getStatus()->getId();

            switch ($status) {
                case BusRegEntity::STATUS_CANCEL:
                    $state = 'cancellation';
                    break;
                case BusRegEntity::STATUS_VAR:
                    $state = 'variation';
                    break;
                default:
                    $state = 'application';
            }

            $description = 'New ' . $state . ' created';
        }

        $data = [
            'category' => TaskEntity::CATEGORY_BUS,
            'subCategory' => TaskEntity::SUBCATEGORY_EBSR,
            'description' => $description . ': [' . $busReg->getRegNo() . ']',
            'actionDate' => date('Y-m-d H:i:s'),
            'assignedToUser' => $this->getCurrentUser()->getId(),
            'assignedToTeam' => 6,
            'busReg' => $busReg->getId(),
            'licence' => $busReg->getLicence()->getId(),
        ];

        return CreateTaskCmd::create($data);
    }

    /**
     * Create a new bus reg
     *
     * @param array $ebsrData
     * @throws Exception\ForbiddenException
     * @return BusRegEntity
     */
    private function createNew(array $ebsrData)
    {
        /** @var BusRegEntity $busReg */
        $busReg = $this->getRepo()->fetchLatestUsingRegNo($ebsrData['licNo'] . '/' . $ebsrData['routeNo']);

        if ($busReg instanceof BusRegEntity) {
            throw new Exception\ForbiddenException('A new application must not reuse an existing registration number');
        }

        /** @var LicenceEntity $licence */
        $licence = $this->getRepo('Licence')->fetchByLicNo($ebsrData['licNo']);

        $newBusReg = BusRegEntity::createNew(
            $licence,
            $this->getRepo()->getRefdataReference(BusRegEntity::STATUS_NEW),
            $this->getRepo()->getRefdataReference(BusRegEntity::STATUS_NEW),
            $ebsrData['subsidised'],
            $ebsrData['busNoticePeriod']
        );

        //quick fix: overwrite the reg no that createNew produced, with the one from EBSR - need to move this logic
        $newBusReg->setRegNo($licence->getLicNo() . '/' . $ebsrData['routeNo']);

        return $newBusReg;
    }

    /**
     * Create a cancellation
     *
     * @param array $ebsrData
     * @throws Exception\ForbiddenException
     * @return BusRegEntity
     */
    private function createCancel(array $ebsrData)
    {
        /** @var BusRegEntity $busReg */
        $busReg = $this->getRepo()->fetchLatestUsingRegNo($ebsrData['licNo'] . '/' . $ebsrData['routeNo']);

        if (!$busReg instanceof BusRegEntity) {
            throw new Exception\ForbiddenException('The bus registration number you provided wasn\'t found');
        }

        if (!$busReg->getStatus()->getId() === BusRegEntity::STATUS_REGISTERED) {
            throw new Exception\ForbiddenException(
                'You can only create a cancellation against a registered bus route'
            );
        }

        //variation should be the same as the variation on the original bus reg
        if ($busReg->getVariationNo() != $ebsrData['variationNo']) {
            throw new Exception\ForbiddenException(
                'Variation number should be 1 greater than the previous variation number'
            );
        }

        return $busReg->createVariation(
            $this->getRepo()->getRefdataReference(BusRegEntity::STATUS_CANCEL),
            $this->getRepo()->getRefdataReference(BusRegEntity::STATUS_CANCEL)
        );
    }

    /**
     * Create a variation
     *
     * @param array $ebsrData
     * @throws Exception\ForbiddenException
     * @return BusRegEntity
     */
    private function createVar(array $ebsrData)
    {
        /** @var BusRegEntity $busReg */
        $busReg = $this->getRepo()->fetchLatestUsingRegNo($ebsrData['licNo'] . '/' . $ebsrData['routeNo']);

        if (!$busReg instanceof BusRegEntity) {
            throw new Exception\ForbiddenException('The bus registration number you provided wasn\'t found');
        }

        if (!$busReg->getStatus()->getId() === BusRegEntity::STATUS_REGISTERED) {
            throw new Exception\ForbiddenException(
                'You can only create a variation against a registered bus route'
            );
        }

        $newBusReg = $busReg->createVariation(
            $this->getRepo()->getRefdataReference(BusRegEntity::STATUS_VAR),
            $this->getRepo()->getRefdataReference(BusRegEntity::STATUS_VAR)
        );

        if ($newBusReg->getVariationNo() != $ebsrData['variationNo']) {
            throw new Exception\ForbiddenException(
                'Variation number should be 1 greater than the previous variation number'
            );
        }

        return $newBusReg;
    }

    /**
     * Ebsr information which couldn't be processed using the pre-migration filters, as we needed Doctrine
     *
     * @param array $ebsrData
     * @return array
     */
    private function processEbsrInformation(array $ebsrData)
    {
        $ebsrData['subsidised'] = $this->getRepo()->getRefdataReference($ebsrData['subsidised']);
        $ebsrData['localAuthoritys'] = $this->processLocalAuthority($ebsrData['localAuthorities']);
        $ebsrData['trafficAreas'] = $this->processTrafficAreas($ebsrData['trafficAreas']);
        $ebsrData['busServiceTypes'] = $this->processServiceTypes($ebsrData['serviceClassifications']);
        $ebsrData['busNoticePeriod'] = $this->getRepo()->getReference(
            BusNoticePeriodEntity::class, $ebsrData['busNoticePeriod']
        );

        return $ebsrData;
    }

    /**
     * Returns collection of service types.
     *
     * @param array $serviceTypes
     * @return ArrayCollection
     */
    private function processServiceTypes(array $serviceTypes)
    {
        $result = new ArrayCollection();

        if (!empty($serviceTypes)) {
            $serviceTypeArray = array_keys($serviceTypes);

            $serviceTypeList = $this->getRepo('BusServiceType')->fetchByTxcName($serviceTypeArray);

            /** @var BusServiceTypeEntity $serviceType */
            foreach ($serviceTypeList as $serviceType) {
                $result->add($serviceType);
            }
        }

        return $result;
    }

    /**
     * @param BusRegEntity $busReg
     * @param array $serviceNumbers
     * @return array
     */
    private function processServiceNumbers(BusRegEntity $busReg, array $serviceNumbers)
    {
        //first make sure we have an empty array collection
        $busReg->setOtherServices(new ArrayCollection());

        foreach ($serviceNumbers as $number) {
            $busReg->addOtherServiceNumber($number);
        }

        return $busReg;
    }

    /**
     * Returns collection of local authorities.
     *
     * @param array $localAuthority
     * @return ArrayCollection
     */
    private function processLocalAuthority(array $localAuthority)
    {
        $result = new ArrayCollection();

        if (!empty($localAuthority)) {
            $laList = $this->getRepo('LocalAuthority')->fetchByTxcName($localAuthority);

            /** @var LocalAuthorityEntity $la */
            foreach ($laList as $la) {
                $result->add($la);
            }
        }

        return $result;
    }

    /**
     * Returns collection of traffic areas.
     *
     * @param array $trafficAreas
     * @return ArrayCollection
     */
    private function processTrafficAreas(array $trafficAreas)
    {
        $result = new ArrayCollection();

        if (!empty($trafficAreas)) {
            $taList = $this->getRepo('TrafficArea')->fetchByTxcName($trafficAreas);

            /** @var TrafficAreaEntity $ta */
            foreach ($taList as $ta) {
                $result->add($ta);
            }
        }

        return $result;
    }
}
