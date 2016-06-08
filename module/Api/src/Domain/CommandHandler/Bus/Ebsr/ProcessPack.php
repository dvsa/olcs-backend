<?php

/**
 * Process Ebsr pack
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Bus\Ebsr;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Domain\Exception;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\Bus\Ebsr\RequestMap as RequestMapQueueCmd;
use Zend\Filter\Decompress;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation as OrganisationEntity;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Ebsr\EbsrSubmission as EbsrSubmissionEntity;
use Dvsa\Olcs\Api\Entity\Bus\BusReg as BusRegEntity;
use Dvsa\Olcs\Api\Entity\Bus\BusNoticePeriod as BusNoticePeriodEntity;
use Dvsa\Olcs\Api\Domain\Repository\BusServiceType as BusServiceTypeRepo;
use Dvsa\Olcs\Api\Entity\Bus\BusServiceType as BusServiceTypeEntity;
use Dvsa\Olcs\Api\Entity\Doc\Document as DocumentEntity;
use Dvsa\Olcs\Api\Entity\System\Category as CategoryEntity;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea as TrafficAreaEntity;
use Dvsa\Olcs\Api\Domain\Repository\TrafficArea as TrafficAreaRepo;
use Dvsa\Olcs\Api\Entity\Bus\LocalAuthority as LocalAuthorityEntity;
use Dvsa\Olcs\Api\Domain\Repository\LocalAuthority as LocalAuthorityRepo;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Domain\Repository\Licence as LicenceRepo;
use Dvsa\Olcs\Api\Entity\Task\Task as TaskEntity;
use Dvsa\Olcs\Api\Domain\Command\Bus\Ebsr\ProcessPack as ProcessPackCmd;
use Dvsa\Olcs\Transfer\Command\Document\Upload as UploadCmd;
use Dvsa\Olcs\Api\Domain\Command\Task\CreateTask as CreateTaskCmd;
use Dvsa\Olcs\Api\Domain\Command\Bus\CreateBusFee as CreateBusFeeCmd;
use Dvsa\Olcs\Api\Domain\Command\Bus\Ebsr\CreateTxcInbox as CreateTxcInboxCmd;
use Dvsa\Olcs\Api\Domain\Command\Email\SendEbsrReceived as SendEbsrReceivedCmd;
use Dvsa\Olcs\Api\Domain\Command\Email\SendEbsrRefreshed as SendEbsrRefreshedCmd;
use Dvsa\Olcs\Api\Domain\Command\Email\SendEbsrErrors as SendEbsrErrorsCmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\UploaderAwareInterface;
use Dvsa\Olcs\Api\Domain\UploaderAwareTrait;
use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\QueueAwareTrait;
use Dvsa\Olcs\Api\Domain\ConfigAwareInterface;
use Dvsa\Olcs\Api\Domain\ConfigAwareTrait;
use Dvsa\Olcs\Api\Domain\FileProcessorAwareInterface;
use Dvsa\Olcs\Api\Domain\FileProcessorAwareTrait;
use Zend\ServiceManager\ServiceLocatorInterface;
use Doctrine\ORM\Query;
use Doctrine\Common\Util\Debug as DoctrineDebug;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactioningCommandHandler;

/**
 * Process Ebsr pack
 */
final class ProcessPack extends AbstractCommandHandler implements
    AuthAwareInterface,
    TransactionedInterface,
    UploaderAwareInterface,
    FileProcessorAwareInterface,
    ConfigAwareInterface
{
    use AuthAwareTrait;
    use UploaderAwareTrait;
    use QueueAwareTrait;
    use FileProcessorAwareTrait;
    use ConfigAwareTrait;

    /**
     * @var int
     *
     * @note We save audit data to the ebsr_submission_result column in the DB. For convenience we've re-used a small
     * amount of this data when creating our error emails. If it is ever decided that we no longer need to store the
     * info, set this value to 1. This will reduce the data stored to a very small amount, without the need for extra
     * work on the emails
     *
     * How many levels of doctrine entities we recurse when saving the audit info
     */
    const DOCTRINE_DEBUG_LEVEL = 2;

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

    protected $xmlStructureInput;

    protected $busRegInput;

    protected $processedDataInput;

    protected $shortNoticeInput;

    /**
     * @var Result
     */
    protected $result;

    /**
     * Creates the service, including the various input filters/validators
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return TransactioningCommandHandler
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $mainServiceLocator = $serviceLocator->getServiceLocator();

        $this->xmlStructureInput = $mainServiceLocator->get('EbsrXmlStructure');
        $this->busRegInput = $mainServiceLocator->get('EbsrBusRegInput');
        $this->processedDataInput = $mainServiceLocator->get('EbsrProcessedDataInput');
        $this->shortNoticeInput = $mainServiceLocator->get('EbsrShortNoticeInput');
        $this->result = new Result();

        return parent::createService($serviceLocator);
    }

    /**
     * Process the EBSR pack
     * Error information is added into the ebsr_submission_result column of the ebsr_submission table
     *
     * @param CommandInterface|ProcessPackCmd $command
     * @return Result
     * @throws \Exception
     */
    public function handleCommand(CommandInterface $command)
    {
        /** @var EbsrSubmissionEntity $ebsrSub */
        $ebsrSub = $this->getRepo('EbsrSubmission')->fetchUsingId($command);
        $ebsrSub->beginValidating($this->getRepo()->getRefdataReference(EbsrSubmissionEntity::VALIDATING_STATUS));
        $this->getRepo('EbsrSubmission')->save($ebsrSub);

        $this->result->addId('ebsrSubmission', $ebsrSub->getId());

        /** @var OrganisationEntity $organisation */
        $organisation = $ebsrSub->getOrganisation();

        /** @var DocumentEntity $doc */
        $doc = $ebsrSub->getDocument();

        $config = $this->getConfig();

        if (!isset($config['ebsr']['tmp_extra_path'])) {
            throw new \RuntimeException('No tmp directory specified in config');
        }

        //set the sub directory of /tmp where we extract the EBSR files
        $this->getFileProcessor()->setSubDirPath($config['ebsr']['tmp_extra_path']);

        try {
            $xmlName = $this->getFileProcessor()->fetchXmlFileNameFromDocumentStore($doc->getIdentifier());
        } catch (\RuntimeException $e) {
            //process the validation failure information
            $this->processValidationFailure($ebsrSub, $doc, ['upload-failure' => $e->getMessage()], '', []);

            return $this->result;
        }

        //validate the xml structure
        $xmlDocContext = ['xml_filename' => $xmlName];
        $ebsrDoc = $this->validateInput('xmlStructure', $ebsrSub, $doc, $xmlName, $xmlName, $xmlDocContext);

        if ($ebsrDoc === false) {
            return $this->result;
        }

        $busRegInputContext = [
            'submissionType' => $ebsrSub->getEbsrSubmissionType()->getId(),
            'organisation' => $organisation
        ];

        //do some pre-doctrine data processing
        $ebsrData = $this->validateInput('busReg', $ebsrSub, $doc, $xmlName, $ebsrDoc, $busRegInputContext);

        if ($ebsrData === false) {
            return $this->result;
        }

        //we now have xml data we can add to our ebsr submission record
        $ebsrSub = $this->addXmlDataToEbsrSubmission($ebsrSub, $ebsrData);

        //get the parts of the data we need doctrine for
        $ebsrData = $this->getDoctrineInformation($ebsrData);

        /** @var BusRegEntity $previousBusReg */
        $previousBusReg = $this->getRepo()->fetchLatestUsingRegNo($ebsrData['existingRegNo']);

        //we now have the data from doctrine, so validate this additional data
        $processedContext = ['busReg' => $previousBusReg];
        $ebsrData = $this->validateInput('processedData', $ebsrSub, $doc, $xmlName, $ebsrData, $processedContext);

        if ($ebsrData === false) {
            return $this->result;
        }

        //we have valid data, so build a bus reg record
        $busReg = $this->createBusReg($ebsrData, $previousBusReg);

        //we can only validate short notice data once we've created the bus reg
        if (!$this->validateInput('shortNotice', $ebsrSub, $doc, $xmlName, $ebsrData, ['busReg' => $busReg])) {
            return $this->result;
        }

        //short notice has passed validation
        if ($busReg->getIsShortNotice() === 'Y') {
            $busReg->getShortNotice()->fromData($ebsrData['busShortNotice']);
        }

        //we've finished validating
        $ebsrSub->finishValidating(
            $this->getRepo()->getRefdataReference(EbsrSubmissionEntity::PROCESSING_STATUS),
            $this->getSubmissionResultData([], $ebsrData)
        );

        //save the submission and the bus reg
        $this->getRepo('EbsrSubmission')->save($ebsrSub);
        $busReg->setEbsrSubmissions(new ArrayCollection([$ebsrSub]));
        $this->getRepo()->save($busReg);

        //update submission status to processed
        $ebsrSub->setBusReg($busReg);
        $this->getRepo('EbsrSubmission')->save($ebsrSub);

        //trigger side effects (persist docs, txc inbox, create task, request a route map, create fee, send email)
        $sideEffects = $this->getSideEffects($ebsrData, $busReg, dirname($xmlName));
        $this->handleSideEffects($sideEffects);

        //we've finished processing
        $ebsrSub->finishProcessing($this->getRepo()->getRefdataReference(EbsrSubmissionEntity::PROCESSED_STATUS));

        $this->result->addMessage(
            $doc->getDescription() . '(' . basename($xmlName) . '): file processed successfully'
        );

        return $this->result;
    }

    /**
     * Calls the specified input filters/validators
     *
     * @param string $filter
     * @param EbsrSubmissionEntity $ebsrSub
     * @param DocumentEntity $doc
     * @param string $xmlName
     * @param array $value
     * @param array $context
     *
     * @return array|bool
     */
    private function validateInput(
        $filter,
        EbsrSubmissionEntity $ebsrSub,
        DocumentEntity $doc,
        $xmlName,
        $value,
        $context = []
    ) {
        $inputFilter = $filter . 'Input';

        $this->$inputFilter->setValue($value);

        if (!$this->$inputFilter->isValid($context)) {
            //create error messages for use by the front end
            $messages = $this->$inputFilter->getMessages();

            //get input values we can use for debug
            $inputValue = $this->$inputFilter->getValue();

            //process the validation failure information
            $this->processValidationFailure($ebsrSub, $doc, $messages, $xmlName, $inputValue);

            return false;
        }

        return $this->$inputFilter->getValue();
    }

    /**
     * Processes a validation failure
     * Sets submission to failed and queues error email
     *
     * @param EbsrSubmissionEntity $ebsrSub
     * @param DocumentEntity $doc
     * @param array $messages
     * @param string $xmlName
     * @param mixed $inputValue
     */
    private function processValidationFailure(
        EbsrSubmissionEntity $ebsrSub,
        DocumentEntity $doc,
        $messages,
        $xmlName,
        $inputValue
    ) {
        $this->addErrorMessages($doc, $messages, $xmlName);

        //save submission result data, used for error messages in emails, and possible debugging later
        $resultData = $this->getSubmissionResultData($messages, $inputValue);
        $this->setEbsrSubmissionFailed($ebsrSub, $resultData);

        //trigger ebsr failure email for the user
        $this->handleSideEffect($this->getEbsrErrorEmailCmd($ebsrSub->getId()));
    }

    /**
     * Creates a serialized string consisting of error messages and input data, saved to ebsrSubmissionResult DB field
     *
     * @param array $errorMessages
     * @param mixed $rawData
     * @return string
     */
    private function getSubmissionResultData($errorMessages, $rawData)
    {
        $errorData = [
            'errors' => $errorMessages,
            'raw_data' => DoctrineDebug::export($rawData, self::DOCTRINE_DEBUG_LEVEL)
        ];

        return serialize($errorData);
    }

    /**
     * Adds error messages to the result object
     *
     * @param DocumentEntity $doc
     * @param array $messages
     * @param string $xmlName
     * @return Result
     */
    private function addErrorMessages(DocumentEntity $doc, array $messages, $xmlName)
    {
        $filename = '';
        $joinedMessages = strtolower(implode(', ', $messages));

        if (!empty($xmlName)) {
            $filename = ' (' . basename($xmlName) . ')';
        }

        $errorMsg = 'Error with ' . $doc->getDescription() . $filename . ': ' . $joinedMessages . ' - not processed';
        $this->result->addId('error_messages', $errorMsg, true);

        return $this->result;
    }

    /**
     * Add in ebsr submission data after the file has been processed
     *
     * @param EbsrSubmissionEntity $ebsrSub
     * @param array $ebsrData
     * @return EbsrSubmissionEntity
     */
    private function addXmlDataToEbsrSubmission(EbsrSubmissionEntity $ebsrSub, array $ebsrData)
    {
        $ebsrSub->setLicenceNo($ebsrData['licNo']);
        $ebsrSub->setVariationNo($ebsrData['variationNo']);
        $ebsrSub->setRegistrationNo($ebsrData['routeNo']);
        $ebsrSub->setOrganisationEmailAddress($ebsrData['organisationEmail']);

        return $ebsrSub;
    }

    /**
     * Sets the EBSR submission to failed, and saves the record
     *
     * @param EbsrSubmissionEntity $ebsrSub
     * @param string $ebsrResultData
     * @return EbsrSubmissionEntity
     */
    private function setEbsrSubmissionFailed(EbsrSubmissionEntity $ebsrSub, $ebsrResultData)
    {
        $ebsrSub->finishValidating(
            $this->getRepo()->getRefdataReference(EbsrSubmissionEntity::FAILED_STATUS),
            $ebsrResultData
        );

        $this->getRepo('EbsrSubmission')->save($ebsrSub);
        return $ebsrSub;
    }

    /**
     * Creates the bus registration
     *
     * @param array $ebsrData
     * @param BusRegEntity|array $previousBusReg
     * @return BusRegEntity
     */
    private function createBusReg(array $ebsrData, $previousBusReg)
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
     * Returns a list of side effects as a result of the EBSR submission success
     *
     * 1. Add supporting documents to the doc store
     * 2. Create TXC inbox record
     * 3. Create a task
     * 4. Queue Transxchange map request
     * 5. Create fee (optional)
     * 6. Queue confirmation email
     *
     * @param array $ebsrData
     * @param BusRegEntity $busReg
     * @param string $docPath
     * @return array
     */
    private function getSideEffects(array $ebsrData, BusRegEntity $busReg, $docPath)
    {
        $sideEffects = $this->persistDocuments($ebsrData, $busReg, $docPath);
        $sideEffects[] = $this->createTxcInboxCmd($busReg->getId());
        $sideEffects[] = $this->createTaskCommand($busReg);
        $sideEffects[] = $this->getRequestMapQueueCmd($busReg->getId());

        $busStatus = $busReg->getStatus()->getId();

        if ($busStatus === BusRegEntity::STATUS_NEW || $busStatus === BusRegEntity::STATUS_VAR) {
            $sideEffects[] = CreateBusFeeCmd::create(['id' => $busReg->getId()]);
        }

        /** @var EbsrSubmissionEntity $ebsrSub */
        $ebsrSub = $busReg->getEbsrSubmissions()->first();

        if ($ebsrSub->isDataRefresh()) {
            $sideEffects[] = $this->getEbsrRefreshedEmailCmd($ebsrSub->getId());
        } else {
            $sideEffects[] = $this->getEbsrReceivedEmailCmd($ebsrSub->getId());
        }

        return $sideEffects;
    }

    /**
     * Returns a side effect to save the supporting documents and schematic map to the doc store
     *
     * @param array $ebsrData
     * @param BusRegEntity $busReg
     * @param string $docPath
     * @return array
     */
    private function persistDocuments(array $ebsrData, BusRegEntity $busReg, $docPath)
    {
        $sideEffects = [];

        //store any supporting documents
        if (isset($ebsrData['documents'])) {
            foreach ($ebsrData['documents'] as $docName) {
                $path = $docPath . '/' . $docName;
                $sideEffects[] = $this->persistSupportingDoc($path, $busReg, $docName, 'Supporting document');
            }
        }

        //store a new map if present
        if (isset($ebsrData['map'])) {
            $path = $docPath . '/' . $ebsrData['map'];
            $sideEffects[] = $this->persistSupportingDoc($path, $busReg, $ebsrData['map'], 'Schematic map');
        }

        return $sideEffects;
    }

    /**
     * Returns an upload command to add the supporting docs to the doc store
     *
     * @param string $content
     * @param BusRegEntity $busReg
     * @param string $filename
     * @param string $description
     * @return UploadCmd
     */
    private function persistSupportingDoc($content, BusRegEntity $busReg, $filename, $description)
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
     * Returns a command to create a txc inbox record
     *
     * @param int $busRegId
     * @return CreateTxcInboxCmd
     */
    private function createTxcInboxCmd($busRegId)
    {
        return CreateTxcInboxCmd::create(['id' => $busRegId]);
    }

    /**
     * Returns a command to queue a transxchange map request
     *
     * @param int $busRegId
     * @return RequestMapQueueCmd
     */
    private function getRequestMapQueueCmd($busRegId)
    {
        return RequestMapQueueCmd::create(['id' => $busRegId, 'scale' => 'small']);
    }

    /**
     * Returns a command to queue a data refresh email
     *
     * @param int $ebsrId
     * @return SendEbsrRefreshedCmd
     */
    private function getEbsrRefreshedEmailCmd($ebsrId)
    {
        return $this->emailQueue(SendEbsrRefreshedCmd::class, ['id' => $ebsrId], $ebsrId);
    }

    /**
     * Returns a command to queue a received email
     *
     * @param int $ebsrId
     * @return SendEbsrReceivedCmd
     */
    private function getEbsrReceivedEmailCmd($ebsrId)
    {
        return $this->emailQueue(SendEbsrReceivedCmd::class, ['id' => $ebsrId], $ebsrId);
    }

    /**
     * Returns a command to queue an error email
     *
     * @param int $ebsrId
     * @return SendEbsrErrorsCmd
     */
    private function getEbsrErrorEmailCmd($ebsrId)
    {
        return $this->emailQueue(SendEbsrErrorsCmd::class, ['id' => $ebsrId], $ebsrId);
    }

    /**
     * Returns a command to create a task
     *
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
            'description' => $description . ': ' . $busReg->getRegNo(),
            'actionDate' => date('Y-m-d H:i:s'),
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
        /**
         * @var LicenceRepo $repo
         * @var LicenceEntity $licence
         */
        $repo = $this->getRepo('Licence');
        $licence = $repo->fetchByLicNoWithoutAdditionalData($ebsrData['licNo']);
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
     * Creates a bus reg variation
     *
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
    private function getDoctrineInformation(array $ebsrData)
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
        $collection = new ArrayCollection();

        if (!empty($serviceTypes)) {
            /** @var BusServiceTypeRepo $repo */
            $repo = $this->getRepo('BusServiceType');
            $serviceTypeArray = array_keys($serviceTypes);

            $serviceTypeList = $repo->fetchByTxcName($serviceTypeArray);

            /** @var BusServiceTypeEntity $serviceType */
            foreach ($serviceTypeList as $serviceType) {
                $collection->add($serviceType);
            }
        }

        return $collection;
    }

    /**
     * Processes additional service numbers
     *
     * @param BusRegEntity $busReg
     * @param array $serviceNumbers
     * @return BusRegEntity
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
        $collection = new ArrayCollection();

        if (!empty($localAuthority)) {
            /** @var LocalAuthorityRepo $repo */
            $repo = $this->getRepo('LocalAuthority');
            $laList = $repo->fetchByTxcName($localAuthority);

            /** @var LocalAuthorityEntity $la */
            foreach ($laList as $la) {
                $collection->add($la);
            }
        }

        return $collection;
    }

    /**
     * Returns collection of local authorities based on the naptan codes.
     *
     * @param array $naptan
     * @return ArrayCollection
     */
    private function processNaptan(array $naptan)
    {
        $collection = new ArrayCollection();

        if (!empty($naptan)) {
            /** @var LocalAuthorityRepo $repo */
            $repo = $this->getRepo('LocalAuthority');
            $laList = $repo->fetchByNaptan($naptan);

            /** @var LocalAuthorityEntity $la */
            foreach ($laList as $la) {
                $collection->add($la);
            }
        }

        return $collection;
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
        $collection = new ArrayCollection();

        if (!empty($trafficAreas)) {
            /** @var TrafficAreaRepo $repo */
            $repo = $this->getRepo('TrafficArea');
            $taList = $repo->fetchByTxcName($trafficAreas);

            /** @var TrafficAreaEntity $ta */
            foreach ($taList as $ta) {
                $collection->add($ta);
            }
        }

        /**
         * @var LocalAuthorityEntity $la
         */
        foreach ($localAuthorities as $la) {
            $ta = $la->getTrafficArea();

            if (!$collection->contains($ta)) {
                $collection->add($ta);
            }
        }

        return $collection;
    }
}
