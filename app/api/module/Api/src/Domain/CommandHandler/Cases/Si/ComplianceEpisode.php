<?php

/**
 * Process Si Compliance Episode
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Cases\Si;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Domain\Exception\Exception;
use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactioningCommandHandler;
use Dvsa\Olcs\Api\Entity\Doc\Document;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Cases\Cases as CaseEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Doc\Document as DocumentEntity;
use Dvsa\Olcs\Api\Entity\System\Category as CategoryEntity;
use Dvsa\Olcs\Api\Entity\Si\SeriousInfringement as SiEntity;
use Dvsa\Olcs\Api\Entity\Si\SiCategory as SiCategoryEntity;
use Dvsa\Olcs\Api\Entity\Si\SiPenaltyErruRequested as PenaltyRequestedEntity;
use Dvsa\Olcs\Api\Entity\Si\SiPenaltyErruImposed as PenaltyImposedEntity;
use Dvsa\Olcs\Api\Entity\Si\ErruRequest as ErruRequestEntity;
use Dvsa\Olcs\Api\Domain\Repository\SiCategoryType as SiCategoryTypeRepo;
use Dvsa\Olcs\Api\Domain\Repository\SiPenaltyImposedType as SiPenaltyImposedTypeRepo;
use Dvsa\Olcs\Api\Domain\Repository\SiPenaltyRequestedType as SiPenaltyRequestedTypeRepo;
use Dvsa\Olcs\Api\Domain\Repository\ErruRequest as ErruRequestRepo;
use Dvsa\Olcs\Api\Domain\Repository\Licence as LicenceRepo;
use Dvsa\Olcs\Api\Domain\Repository\Country as CountryRepo;
use Dvsa\Olcs\Api\Domain\Command\Cases\Si\ComplianceEpisode as ComplianceEpisodeCmd;
use Dvsa\Olcs\Transfer\Command\Document\UpdateDocumentLinks as UpdateDocLinksCmd;
use Dvsa\Olcs\Api\Domain\Command\Task\CreateTask as CreateTaskCmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\UploaderAwareInterface;
use Dvsa\Olcs\Api\Domain\UploaderAwareTrait;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\DocumentShare\Data\Object\File;

/**
 * Process Si Compliance Episode
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
final class ComplianceEpisode extends AbstractCommandHandler implements TransactionedInterface, UploaderAwareInterface
{
    use UploaderAwareTrait;

    protected $repoServiceName = 'Cases';

    protected $extraRepos = [
        'Licence',
        'Country',
        'SiCategory',
        'SiCategoryType',
        'SiPenaltyRequestedType',
        'SiPenaltyImposedType',
        'ErruRequest',
        'Document'
    ];

    protected $xmlStructureInput;

    protected $complianceEpisodeInput;

    protected $seriousInfringementInput;

    /**
     * si category doctrine information
     *
     * @var array
     */
    protected $siCategory = [];

    /**
     * requested erru penalty doctrine information
     *
     * @var array
     */
    protected $requestedPen = [];

    /**
     * imposed erru penalty doctrine information
     *
     * @var array
     */
    protected $imposedPen = [];

    /**
     * category type doctrine information
     *
     * @var array
     */
    protected $siCategoryType = [];

    /**
     * common data which will be standard across each infringement
     *
     * @var array
     */
    protected $commonData = [];

    /**
     * @var Result
     */
    protected $result;

    /**
     * create service
     *
     * @param ServiceLocatorInterface $serviceLocator service locator
     *
     * @return TransactioningCommandHandler
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $mainServiceLocator = $serviceLocator->getServiceLocator();

        $this->xmlStructureInput = $mainServiceLocator->get('ComplianceXmlStructure');
        $this->complianceEpisodeInput = $mainServiceLocator->get('ComplianceEpisodeInput');
        $this->seriousInfringementInput = $mainServiceLocator->get('SeriousInfringementInput');

        return parent::createService($serviceLocator);
    }

    /**
     * Handle command to create erru compliance episode
     *
     * @param CommandInterface|ComplianceEpisodeCmd $command the command
     *
     * @return Result
     * @throws \Exception
     */
    public function handleCommand(CommandInterface $command)
    {
        /**
         * @var \DOMDocument $xmlDomDocument
         * @var Document $document
         * @var array $erruData
         */
        $requestDocument = $this->getRepo('Document')->fetchUsingId($command);

        /** @var File $xmlFile */
        $xmlFile = $this->getUploader()->download($requestDocument->getIdentifier());

        $xmlDomDocument = $this->validateInput('xmlStructure', $xmlFile->getContent(), []);

        //do some pre-doctrine data processing
        $erruData = $this->validateInput('complianceEpisode', $xmlDomDocument, []);

        try {
            //get the parts of the data we need doctrine for
            $this->commonData = $this->getCommonData($erruData);
        } catch (NotFoundException $e) {
            //will result in a 400 response from XML controller, which is what we're looking for
            throw new Exception('some data was not correct');
        }

        $case = $this->generateCase($erruData, $requestDocument);

        //there can be more than one serious infringement per request
        foreach ($erruData['si'] as $si) {
            //format/validate si data
            $si = $this->validateInput('seriousInfringement', $si, []);

            //doctrine penalty data for this si (we may have this already from a previous si)
            $this->addDoctrinePenaltyData($si['imposedErrus'], $si['requestedErrus']);
            $this->addDoctrineCategoryTypeData($si['siCategoryType']);

            $case->getSeriousInfringements()->add($this->getSi($case, $si));
        }

        $this->getRepo()->save($case);
        $this->result->merge(
            $this->handleSideEffects(
                [
                    $this->createTaskCmd($case),
                    $this->createUpdateDocLinksCmd($requestDocument, $case, $this->commonData['licence'])
                ]
            )
        );
        $this->result->addId('case', $case->getId());

        return $this->result;
    }

    /**
     * Gets a serious infringement entity
     *
     * @param CaseEntity $case case entity
     * @param array      $si   array of serious infringement information
     *
     * @return SiEntity
     */
    private function getSi(CaseEntity $case, array $si)
    {
        $siEntity = new SiEntity(
            $case,
            $si['checkDate'],
            $si['infringementDate'],
            $this->getRepo('SiCategory')->fetchById(SiCategoryEntity::ERRU_DEFAULT_CATEGORY),
            $this->siCategoryType[$si['siCategoryType']]
        );

        $siEntity->addImposedErrus($this->getImposedErruCollection($siEntity, $si['imposedErrus']));
        $siEntity->addRequestedErrus($this->getRequestedErruCollection($siEntity, $si['requestedErrus']));

        return $siEntity;
    }

    /**
     * Returns an array collection of imposed errus
     *
     * @param SiEntity $si           serious infringement entity
     * @param array    $imposedErrus array of imposed errus
     *
     * @return ArrayCollection
     */
    private function getImposedErruCollection(SiEntity $si, $imposedErrus)
    {
        $imposedErruCollection = new ArrayCollection();

        foreach ($imposedErrus as $imposedErru) {
            $imposedEntity = new PenaltyImposedEntity(
                $si,
                $this->imposedPen['siPenaltyImposedType'][$imposedErru['siPenaltyImposedType']],
                $this->imposedPen['executed'][$imposedErru['executed']],
                $imposedErru['startDate'],
                $imposedErru['endDate'],
                $imposedErru['finalDecisionDate']
            );

            $imposedErruCollection->add($imposedEntity);
        }

        return $imposedErruCollection;
    }

    /**
     * Returns an array collection of requested errus
     *
     * @param SiEntity $si             serious infringement entity
     * @param array    $requestedErrus array of requested errus
     *
     * @return ArrayCollection
     */
    private function getRequestedErruCollection(SiEntity $si, $requestedErrus)
    {
        $requestedErruCollection = new ArrayCollection();

        foreach ($requestedErrus as $requestedErru) {
            $penalty = $this->requestedPen['siPenaltyRequestedType'][$requestedErru['siPenaltyRequestedType']];
            $requestedErruCollection->add(new PenaltyRequestedEntity($si, $penalty, $requestedErru['duration']));
        }

        return $requestedErruCollection;
    }

    /**
     * Builds the case entity
     *
     * @param array    $erruData        array of erru data
     * @param Document $requestDocument request document entity
     *
     * @return CaseEntity
     */
    private function generateCase(array $erruData, Document $requestDocument)
    {
        $case = new CaseEntity(
            new \DateTime(),
            $this->getRepo()->getRefdataReference(CaseEntity::LICENCE_CASE_TYPE),
            $this->getCaseCategories(),
            new ArrayCollection(),
            null,
            $this->commonData['licence'],
            null,
            null,
            'ERRU case automatically created'
        );

        $erruRequest = $this->getErruRequest(
            $case,
            $requestDocument,
            $erruData['originatingAuthority'],
            $erruData['transportUndertakingName'],
            $erruData['vrm']
        );

        $case->setErruRequest($erruRequest);

        return $case;
    }

    /**
     * Builds the ErruRequest entity
     *
     * @param CaseEntity $case                 case entity
     * @param Document $requestDocument        request document entity
     * @param string $originatingAuthority     originating authority
     * @param string $transportUndertakingName transport undertaking name
     * @param string $vrm                      vrm
     *
     * @return ErruRequestEntity
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    private function getErruRequest(
        CaseEntity $case,
        Document $requestDocument,
        $originatingAuthority,
        $transportUndertakingName,
        $vrm
    ) {
        return new ErruRequestEntity(
            $case,
            $this->getRepo()->getRefdataReference(ErruRequestEntity::DEFAULT_CASE_TYPE),
            $this->commonData['memberState'],
            $requestDocument,
            $originatingAuthority,
            $transportUndertakingName,
            $vrm,
            $this->commonData['notificationNumber'],
            $this->commonData['workflowId']
        );
    }

    /**
     * Gets a list of case categories
     *
     * @return ArrayCollection
     */
    private function getCaseCategories()
    {
        return new ArrayCollection([$this->getRepo()->getRefdataReference(CaseEntity::ERRU_DEFAULT_CASE_CATEGORY)]);
    }

    /**
     * Gets doctrine category type data for each serious infringement, if we've already retrieved the data previously,
     * we don't do so again
     *
     * @param int $categoryType category type id
     *
     * @return void
     */
    private function addDoctrineCategoryTypeData($categoryType)
    {
        if (!isset($this->siCategoryType[$categoryType])) {
            /** @var SiCategoryTypeRepo $categoryTypeRepo */
            $categoryTypeRepo = $this->getRepo('SiCategoryType');

            $this->siCategoryType[$categoryType] = $categoryTypeRepo->fetchById($categoryType);
        }
    }

    /**
     * Gets doctrine penalty data for each serious infringement, if we've already retrieved the data previously,
     * we don't do so again
     *
     * @param array $imposedErruData   imposed erru data
     * @param array $requestedErruData requested erru data
     */
    private function addDoctrinePenaltyData(array $imposedErruData, array $requestedErruData)
    {
        /**
         * @var SiPenaltyRequestedTypeRepo $imposedRepo
         * @var SiPenaltyImposedTypeRepo $requestedRepo
         */
        $imposedRepo = $this->getRepo('SiPenaltyImposedType');
        $requestedRepo = $this->getRepo('SiPenaltyRequestedType');
        $executedKey = 'executed';
        $imposedKey = 'siPenaltyImposedType';
        $requestedKey = 'siPenaltyRequestedType';

        foreach ($imposedErruData as $imposedErru) {
            //doctrine entity data for executed RefData
            $executedValue = $imposedErru[$executedKey];

            if (!isset($this->imposedPen[$executedKey][$executedValue])) {
                $this->imposedPen[$executedKey][$executedValue] = $this->getRepo()->getRefdataReference($executedValue);
            }

            //doctrine data for siPenaltyImposedType
            $imposedValue = $imposedErru[$imposedKey];

            if (!isset($this->imposedPen[$imposedKey][$imposedValue])) {
                $this->imposedPen[$imposedKey][$imposedValue] = $imposedRepo->fetchById($imposedValue);
            }
        }

        foreach ($requestedErruData as $requestedErru) {
            //doctrine data for siPenaltyRequestedType
            $requestedValue = $requestedErru[$requestedKey];

            if (!isset($this->requestedPen[$requestedKey][$requestedValue])) {
                $this->requestedPen[$requestedKey][$requestedValue] = $requestedRepo->fetchById($requestedValue);
            }
        }
    }

    /**
     * Erru information which couldn't be processed using the pre-migration filters, as we needed Doctrine.
     * This is common information that can be used on all serious infringements in the request.
     *
     * @param array $erruData array of erru data
     *
     * @throws NotFoundException
     * @throws Exception
     * @return array
     */
    private function getCommonData(array $erruData)
    {
        /**
         * @var ErruRequestRepo $erruRequestRepo
         * @var LicenceRepo $licenceRepo
         * @var CountryRepo $countryRepo
         */
        $erruRequestRepo = $this->getRepo('ErruRequest');

        //check we don't already have an erru request with this workflow id
        if ($erruRequestRepo->existsByWorkflowId($erruData['workflowId'])) {
            throw new Exception('erru request with this workflow id already exists');
        }

        $licenceRepo = $this->getRepo('Licence');
        $countryRepo = $this->getRepo('Country');

        $this->commonData = [
            'licence' => $licenceRepo->fetchByLicNoWithoutAdditionalData($erruData['licenceNumber']),
            'memberState' => $countryRepo->fetchById($erruData['memberStateCode']),
            'notificationNumber' => $erruData['notificationNumber'],
            'workflowId' => $erruData['workflowId']
        ];

        return $this->commonData;
    }

    /**
     * Validates the input
     *
     * @param string $filter  filter bring called
     * @param mixed  $value   input value
     * @param array  $context input context
     *
     * @throws Exception
     * @return mixed
     */
    private function validateInput($filter, $value, $context = [])
    {
        $inputFilter = $filter . 'Input';
        $this->$inputFilter->setValue($value);

        if (!$this->$inputFilter->isValid($context)) {
            throw new Exception('Validation error: ' . implode(',', $this->$inputFilter->getMessages()));
        }

        return $this->$inputFilter->getValue();
    }

    /**
     * Creates a task
     *
     * @param CaseEntity $case case entity
     *
     * @return CreateTaskCmd
     */
    private function createTaskCmd($case)
    {
        $data = [
            'category' => CategoryEntity::CATEGORY_COMPLIANCE,
            'subCategory' => CategoryEntity::TASK_SUB_CATEGORY_NR,
            'description' => 'ERRU case has been automatically created',
            'actionDate' => date('Y-m-d', strtotime('+7 days')),
            'urgent' => 'Y',
            'case' => $case->getId(),
            'licence' => $case->getLicence()->getId(),
        ];

        return CreateTaskCmd::create($data);
    }

    /**
     * Updates the document record with case and licence ids
     *
     * @param DocumentEntity $document document entity
     * @param CaseEntity     $case     case entity
     * @param Licence        $licence  licence entity
     *
     * @return UpdateDocLinksCmd
     */
    private function createUpdateDocLinksCmd(DocumentEntity $document, CaseEntity $case, LicenceEntity $licence)
    {
        $data = [
            'id' => $document->getId(),
            'case' => $case->getId(),
            'licence' => $licence->getId(),
        ];

        return UpdateDocLinksCmd::create($data);
    }
}
