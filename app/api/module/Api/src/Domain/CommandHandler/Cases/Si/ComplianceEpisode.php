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
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Cases\Cases as CaseEntity;
use Dvsa\Olcs\Api\Entity\Si\SeriousInfringement as SiEntity;
use Dvsa\Olcs\Api\Entity\Si\SiCategory as SiCategoryEntity;
use Dvsa\Olcs\Api\Entity\Si\SiPenaltyErruRequested as PenaltyRequestedEntity;
use Dvsa\Olcs\Api\Entity\Si\SiPenaltyErruImposed as PenaltyImposedEntity;
use Dvsa\Olcs\Api\Entity\Task\Task as TaskEntity;
use Dvsa\Olcs\Api\Entity\Si\ErruRequest as ErruRequestEntity;
use Dvsa\Olcs\Api\Domain\Repository\SiCategoryType as SiCategoryTypeRepo;
use Dvsa\Olcs\Api\Domain\Repository\SiPenaltyImposedType as SiPenaltyImposedTypeRepo;
use Dvsa\Olcs\Api\Domain\Repository\SiPenaltyRequestedType as SiPenaltyRequestedTypeRepo;
use Dvsa\Olcs\Api\Domain\Repository\SeriousInfringement as SiRepo;
use Dvsa\Olcs\Api\Domain\Repository\ErruRequest as ErruRequestRepo;
use Dvsa\Olcs\Api\Domain\Repository\Licence as LicenceRepo;
use Dvsa\Olcs\Api\Domain\Repository\Country as CountryRepo;
use Dvsa\Olcs\Transfer\Command\Cases\Si\ComplianceEpisode as ComplianceEpisodeCmd;
use Dvsa\Olcs\Api\Domain\Command\Task\CreateTask as CreateTaskCmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Process Si Compliance Episode
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
final class ComplianceEpisode extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Cases';

    protected $extraRepos = [
        'Licence',
        'Country',
        'SeriousInfringement',
        'SiCategory',
        'SiCategoryType',
        'SiPenaltyRequestedType',
        'SiPenaltyImposedType',
        'ErruRequest'
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

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $mainServiceLocator = $serviceLocator->getServiceLocator();

        $this->xmlStructureInput = $mainServiceLocator->get('ComplianceXmlStructure');
        $this->complianceEpisodeInput = $mainServiceLocator->get('ComplianceEpisodeInput');
        $this->seriousInfringementInput = $mainServiceLocator->get('SeriousInfringementInput');

        $this->result = new Result();

        return parent::createService($serviceLocator);
    }

    /**
     * @param CommandInterface $command
     * @return Result
     * @throws \Exception
     */
    public function handleCommand(CommandInterface $command)
    {
        /**
         * @var ComplianceEpisodeCmd $command
         * @var \DomDocument $xmlDomDocument
         * @var array $erruData
         */
        $xmlDomDocument = $this->validateInput('xmlStructure', $command->getXml(), []);

        //do some pre-doctrine data processing
        $erruData = $this->validateInput('complianceEpisode', $xmlDomDocument, []);

        try {
            //get the parts of the data we need doctrine for
            $this->commonData = $this->getCommonData($erruData);
        } catch (NotFoundException $e) {
            //will result in a 400 response from XML controller, which is what we're looking for
            throw new Exception('some data was not correct');
        }

        $case = $this->generateCase($erruData);

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
        $this->result->merge($this->handleSideEffect($this->createTaskCmd($case)));
        $this->result->addId('case', $case->getId());

        return $this->result;
    }

    /**
     * @param CaseEntity $case
     * @param array $si
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
     * @param SiEntity $si
     * @param array $imposedErrus
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
     * @param SiEntity $si
     * @param array $requestedErrus
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
     * @param array $erruData
     * @return CaseEntity
     */
    private function generateCase($erruData)
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
     * @param CaseEntity $case
     * @param $originatingAuthority
     * @param $transportUndertakingName
     * @param $vrm
     *
     * @return ErruRequestEntity
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    private function getErruRequest(CaseEntity $case, $originatingAuthority, $transportUndertakingName, $vrm)
    {
        return new ErruRequestEntity(
            $case,
            $this->getRepo()->getRefdataReference(ErruRequestEntity::DEFAULT_CASE_TYPE),
            $this->commonData['memberState'],
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
     * @param int $categoryType
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
     * @param array $imposedErruData
     * @param array $requestedErruData
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
                $this->imposedPen[$executedKey][$executedValue] = $this->getRepo()->getRefDataReference($executedValue);
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
     * @param array $erruData
     * @throws NotFoundException
     * @throws Exception
     *
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
     * @param string $filter
     * @param mixed $value
     * @param array $context
     *
     * @throws Exception
     *
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
     * @param CaseEntity $case
     * @return CreateTaskCmd
     */
    private function createTaskCmd($case)
    {
        $data = [
            'category' => TaskEntity::CATEGORY_NR,
            'subCategory' => TaskEntity::SUBCATEGORY_NR,
            'description' => 'ERRU case has been automatically created',
            'actionDate' => date('Y-m-d', strtotime('+7 days')),
            'urgent' => 'Y',
            'case' => $case->getId(),
            'licence' => $case->getLicence()->getId(),
        ];

        return CreateTaskCmd::create($data);
    }
}
