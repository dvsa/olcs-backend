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
use Dvsa\Olcs\Api\Domain\Command\Bus\CreateBusFee as CreateBusFeeCmd;
use Dvsa\Olcs\Api\Domain\Command\Bus\Ebsr\CreateTxcInbox as CreateTxcInboxCmd;
use Dvsa\Olcs\Api\Domain\Command\Email\SendEbsrReceived as SendEbsrReceivedCmd;
use Dvsa\Olcs\Api\Domain\Command\Email\SendEbsrRefreshed as SendEbsrRefreshedCmd;
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

    protected $repoServiceName = 'Bus';

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

    protected $processedDataInput;

    /**
     * @var FileProcessor
     */
    protected $fileProcessor;

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $mainServiceLocator = $serviceLocator->getServiceLocator();

        $this->xmlStructure = $mainServiceLocator->get('EbsrXmlStructure');
        $this->busRegInput = $mainServiceLocator->get('EbsrBusRegInput');
        $this->processedDataInput = $mainServiceLocator->get('EbsrProcessedDataInput');
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

        /** @var OrganisationEntity $organisation */
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
                $result = $this->addErrorMessages($result, $document, [$e->getMessage()], '');
                $this->setEbsrSubmissionFailed($ebsrSubmission);

                continue;
            }

            $this->xmlStructure->setValue($xmlFilename);

            if (!$this->xmlStructure->isValid(['xml_filename' => $xmlFilename])) {
                $invalidPacks++;
                $result = $this->addErrorMessages($result, $document, $this->xmlStructure->getMessages(), $xmlFilename);
                $this->setEbsrSubmissionFailed($ebsrSubmission);

                continue;
            }

            $ebsrDoc = $this->xmlStructure->getValue();

            $this->busRegInput->setValue($ebsrDoc);

            $busRegInputContext = [
                'submissionType' => $command->getSubmissionType(),
                'organisation' => $organisation
            ];

            if (!$this->busRegInput->isValid($busRegInputContext)) {
                $invalidPacks++;
                $result = $this->addErrorMessages($result, $document, $this->busRegInput->getMessages(), $xmlFilename);
                $this->setEbsrSubmissionFailed($ebsrSubmission);

                continue;
            }

            //this is ebsr data we could validate without the help of doctrine
            $ebsrData = $this->busRegInput->getValue();

            //get the parts of the data we need doctrine for
            $ebsrData = $this->processEbsrInformation($ebsrData);

            /** @var BusRegEntity $previousBusReg */
            $previousBusReg = $this->getRepo()->fetchLatestUsingRegNo($ebsrData['existingRegNo']);

            //now do the validation we can only do post doctrine
            $this->processedDataInput->setValue($ebsrData);

            if (!$this->processedDataInput->isValid(['busReg' => $previousBusReg])) {
                $invalidPacks++;
                $messages = $this->processedDataInput->getMessages();
                $result = $this->addErrorMessages($result, $document, $messages, $xmlFilename);
                $this->setEbsrSubmissionFailed($ebsrSubmission);

                continue;
            }

            $ebsrData = $this->processedDataInput->getValue();

            $ebsrSubmission->updateStatus(
                $this->getRepo()->getRefdataReference(EbsrSubmissionEntity::VALIDATED_STATUS)
            );

            $ebsrSubmission->setLicenceNo($ebsrData['licNo']);
            $ebsrSubmission->setVariationNo($ebsrData['variationNo']);
            $ebsrSubmission->setRegistrationNo($ebsrData['routeNo']);
            $ebsrSubmission->setOrganisationEmailAddress($ebsrData['organisationEmail']);
            $this->getRepo('EbsrSubmission')->save($ebsrSubmission);

            try {
                $busReg = $this->createBusReg($ebsrData);
            } catch (Exception\NotFoundException $e) {
                $invalidPacks++;
                $result = $this->addErrorMessages($result, $document, $e->getMessages(), $xmlFilename);
                $this->setEbsrSubmissionFailed($ebsrSubmission);

                continue;
            } catch (Exception\ForbiddenException $e) {
                $invalidPacks++;
                $result = $this->addErrorMessages($result, $document, $e->getMessages(), $xmlFilename);
                $this->setEbsrSubmissionFailed($ebsrSubmission);

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
     * @param Result $result
     * @param DocumentEntity $document
     * @param array $messages
     * @param string $xmlFilename
     * @return Result
     */
    private function addErrorMessages(Result $result, DocumentEntity $document, array $messages, $xmlFilename)
    {
        $filename = '';

        if (!empty($xmlFilename)) {
            $filename = ' (' . basename($xmlFilename) . ')';
        }

        $result->addId(
            'error_messages',
            'Error with ' . $document->getDescription() . $filename .
            ': ' . strtolower(implode(', ', $messages)) . ' - not processed',
            true
        );

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

    private function setEbsrSubmissionFailed($ebsrSubmission)
    {
        $ebsrSubmission->updateStatus(
            $this->getRepo()->getRefdataReference(EbsrSubmissionEntity::FAILED_STATUS)
        );

        $this->getRepo('EbsrSubmission')->save($ebsrSubmission);
        return $ebsrSubmission;
    }

    /**
     * Creates the bus registration
     *
     * @param array $ebsrData
     * @param BusRegEntity $previousBusReg
     * @return BusRegEntity
     */
    private function createBusReg(array $ebsrData, BusRegEntity $previousBusReg)
    {
        //decide what to do based on txcAppType
        switch ($ebsrData['txcAppType']) {
            case 'new':
                $busReg = $this->createNew($ebsrData);
                break;
            case 'cancel':
                $busReg = $this->createVariation($previousBusReg, BusRegEntity::STATUS_CANCEL);
                break;
            default:
                $busReg = $this->createVariation($previousBusReg, BusRegEntity::STATUS_VAR);
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
        $sideEffects[] = $this->createTxcInboxCmd($busReg->getId());
        $sideEffects[] = $this->createTaskCommand($busReg);
        $sideEffects[] = $this->getRequestMapQueueCmd($busReg->getId());

        $busStatus = $busReg->getStatus()->getId();

        if ($busStatus === BusRegEntity::STATUS_NEW || $busStatus === BusRegEntity::STATUS_VAR) {
            $sideEffects[] = CreateBusFeeCmd::create(['id' => $busReg->getId()]);
        }

        /** @var EbsrSubmissionEntity $ebsrSubmission */
        $ebsrSubmission = $busReg->getEbsrSubmissions()->first();

        if ($ebsrSubmission->isDataRefresh()) {
            $sideEffects[] = $this->getEbsrRefreshedEmailCmd($ebsrSubmission->getId());
        } else {
            $sideEffects[] = $this->getEbsrReceivedEmailCmd($ebsrSubmission->getId());
        }

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
     * @return CreateTxcInboxCmd
     */
    private function createTxcInboxCmd($busRegId)
    {
        return CreateTxcInboxCmd::create(['id' => $busRegId]);
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
     * @param int $ebsrId
     * @return SendEbsrRefreshedCmd
     */
    private function getEbsrRefreshedEmailCmd($ebsrId)
    {
        return SendEbsrRefreshedCmd::create(['id' => $ebsrId]);
    }

    /**
     * @param int $ebsrId
     * @return SendEbsrReceivedCmd
     */
    private function getEbsrReceivedEmailCmd($ebsrId)
    {
        return SendEbsrReceivedCmd::create(['id' => $ebsrId]);
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
        /** @var LicenceEntity $licence */
        $licence = $this->getRepo('Licence')->fetchByLicNo($ebsrData['licNo']);
        $refDataStatus = $this->getRepo()->getRefdataReference(BusRegEntity::STATUS_NEW);

        $newBusReg = BusRegEntity::createNew(
            $licence,
            $refDataStatus,
            $refDataStatus,
            $ebsrData['subsidised'],
            $ebsrData['busNoticePeriod']
        );

        //quick fix: overwrite the reg no that createNew produced, with the one from EBSR - need to move this logic
        $newBusReg->setRegNo($licence->getLicNo() . '/' . $ebsrData['routeNo']);

        return $newBusReg;
    }

    /**
     * @param BusRegEntity $busReg
     * @param string $status
     * @return BusRegEntity
     */
    private function createVariation(BusRegEntity $busReg, $status)
    {
        $refDataStatus = $this->getRepo()->getRefdataReference($status);
        return $busReg->createVariation($refDataStatus, $refDataStatus);
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
        $ebsrData['naptanAuthorities'] = $this->processNaptan($ebsrData['naptan']);
        $ebsrData['localAuthoritys'] = $this->processLocalAuthority($ebsrData['localAuthorities']);
        $ebsrData['trafficAreas'] = $this->processTrafficAreas($ebsrData['trafficAreas'], $ebsrData['localAuthoritys']);
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
     * Returns collection of local authorities based on the naptan codes.
     *
     * @param array $naptan
     * @return ArrayCollection
     */
    private function processNaptan(array $naptan)
    {
        $result = new ArrayCollection();

        if (!empty($naptan)) {
            $laList = $this->getRepo('LocalAuthority')->fetchByNaptan($naptan);

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
     * @param ArrayCollection $localAuthorities
     * @return ArrayCollection
     */
    private function processTrafficAreas(array $trafficAreas, ArrayCollection $localAuthorities)
    {
        $result = new ArrayCollection();

        if (!empty($trafficAreas)) {
            $taList = $this->getRepo('TrafficArea')->fetchByTxcName($trafficAreas);

            /** @var TrafficAreaEntity $ta */
            foreach ($taList as $ta) {
                $result->add($ta);
            }
        }

        /**
         * @var LocalAuthorityEntity $la
         */
        foreach ($localAuthorities as $la) {
            $ta = $la->getTrafficArea();

            if (!$result->contains($ta)) {
                $result->add($ta);
            }
        }

        return $result;
    }
}
