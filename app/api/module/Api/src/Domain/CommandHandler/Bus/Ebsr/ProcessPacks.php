<?php

/**
 * Process Ebsr packs
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Bus\Ebsr;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Service\Ebsr\FileProcessor;
use Dvsa\Olcs\Api\Filesystem\Filesystem;
use Zend\Filter\Decompress;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation as OrganisationEntity;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Ebsr\EbsrSubmission as EbsrSubmissionEntity;
use Dvsa\Olcs\Api\Entity\Bus\BusReg as BusRegEntity;
use Dvsa\Olcs\Api\Entity\Bus\BusShortNotice as BusShortNoticeEntity;
use Dvsa\Olcs\Api\Entity\Bus\BusNoticePeriod as BusNoticePeriodEntity;
use Dvsa\Olcs\Api\Entity\Bus\BusRegOtherService as BusRegOtherServiceEntity;
use Dvsa\Olcs\Api\Entity\Bus\BusServiceType as BusServiceTypeEntity;
use Dvsa\Olcs\Api\Entity\Doc\Document as DocumentEntity;
use Dvsa\Olcs\Api\Entity\System\Category as CategoryEntity;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea as TrafficAreaEntity;
use Dvsa\Olcs\Api\Entity\Bus\LocalAuthority as LocalAuthorityEntity;
use Dvsa\Olcs\Transfer\Command\Bus\Ebsr\ProcessPacks as ProcessPacksCmd;
use Dvsa\Olcs\Transfer\Command\Document\Upload as UploadCmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\UploaderAwareInterface;
use Dvsa\Olcs\Api\Domain\UploaderAwareTrait;
use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Zend\ServiceManager\ServiceLocatorInterface;
use Doctrine\ORM\Query;

/**
 * Process Ebsr packs
 */
final class ProcessPacks extends AbstractCommandHandler
    implements AuthAwareInterface, TransactionedInterface, UploaderAwareInterface
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
        'LocalAuthority'
    ];

    protected $xmlStructure;

    protected $busRegInput;

    protected $fileProcessor;

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $mainServiceLocator = $serviceLocator->getServiceLocator();

        $this->xmlStructure = $mainServiceLocator->get('EbsrXmlStructure');
        $this->busRegInput = $mainServiceLocator->get('EbsrBusRegInput');
        $this->fileProcessor = $mainServiceLocator->get(FileProcessor::class);

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

        foreach ($packs as $packId) {
            /** @var DocumentEntity $document */
            $document = $this->getRepo('Document')->fetchById($packId);
            $ebsrSubmission = $this->createEbsrSubmission($organisation, $document, $command->getSubmissionType());
            $this->getRepo('EbsrSubmission')->save($ebsrSubmission);
            $result->addId('ebsrSubmission_' . $ebsrSubmission->getId(), $ebsrSubmission->getId());
            $result->addMessage('Ebsr submission added');

            $xmlFilename = $this->fileProcessor->fetchXmlFileNameFromDocumentStore($document->getIdentifier());

            $this->xmlStructure->setValue($xmlFilename);

            if (!$this->xmlStructure->isValid()) {
                //we'll need to abort here and return messages
                $messages = $this->xmlStructure->getMessages();
            }

            $ebsrDoc = $this->xmlStructure->getValue();

            $this->busRegInput->setValue($ebsrDoc);
            $ebsrData = $this->busRegInput->getValue();

            $ebsrSubmission->updateStatus(
                $this->getRepo()->getRefdataReference(EbsrSubmissionEntity::VALIDATED_STATUS)
            );

            $this->getRepo('EbsrSubmission')->save($ebsrSubmission);

            $ebsrData = $this->processEbsrInformation($ebsrData);

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
            //$busReg = $this->processServiceNumbers($busReg, $ebsrData['otherServiceNumbers']);

            if (!empty($ebsrData['busShortNotice'])) {
                $busReg->getShortNotice()->fromData($ebsrData['busShortNotice']);
            }

            $this->getRepo()->save($busReg);

            $sideEffects = $this->persistDocuments($ebsrData, $busReg, $document);

            $ebsrSubmission->updateStatus(
                $this->getRepo()->getRefdataReference(EbsrSubmissionEntity::PROCESSED_STATUS)
            );
            $ebsrSubmission->setBusReg($busReg);

            $this->getRepo('EbsrSubmission')->save($ebsrSubmission);

            $result->merge($this->handleSideEffects($sideEffects));
        }

        return $result;
    }

    private function prepareBusRegData($ebsrData)
    {
        //@todo add mappings in here shortly
        $busRegData = $ebsrData;
        unset($busRegData['documents']);
    }

    /**
     * @param array $ebsrData
     * @param BusRegEntity $busReg
     * @param DocumentEntity $document
     * @return array
     */
    private function persistDocuments(array $ebsrData, BusRegEntity $busReg, DocumentEntity $document)
    {
        $sideEffects = [];

        //store any supporting documents
        if (isset($ebsrData['documents'])) {
            foreach ($ebsrData['documents'] as $content) {
                $sideEffects[] = $this->persistSupportingDocument(
                    $content,
                    $busReg,
                    basename($document),
                    'Supporting document'
                );
            }
        }

        //store a new map if present
        if (isset($ebsrData['map'])) {
            $sideEffects[] = $this->persistSupportingDocument(
                $ebsrData['map'],
                $busReg,
                basename($document),
                'Schematic map'
            );
        }

        return $sideEffects;
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
     * @param string $content
     * @param BusRegEntity $busReg
     * @param string $filename
     * @param string $description
     * @return UploadCmd
     */
    private function persistSupportingDocument($content, BusRegEntity $busReg, $filename, $description)
    {
        $data = [
            'content' => base64_encode(trim($content)),
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
     * Create a new bus reg
     *
     * @param array $ebsrData
     * @return BusRegEntity
     */
    private function createNew($ebsrData)
    {
        $licence = $this->getRepo('Licence')->fetchByLicNo($ebsrData['licNo']);

        return BusRegEntity::createNew(
            $licence,
            $this->getRepo()->getRefdataReference(BusRegEntity::STATUS_NEW),
            $this->getRepo()->getRefdataReference(BusRegEntity::STATUS_NEW),
            $ebsrData['subsidised'],
            $ebsrData['busNoticePeriod']
        );
    }

    /**
     * Create a cancellation
     *
     * @param array $ebsrData
     * @return BusRegEntity
     */
    private function createCancel($ebsrData)
    {
        $busReg = $this->getRepo()->fetchLatestUsingRegNo($ebsrData['licNo'] . '/' . $ebsrData['routeNo']);

        return $busReg->createVariation(
            $this->getRepo()->getRefdataReference(BusRegEntity::STATUS_CANCEL),
            $this->getRepo()->getRefdataReference(BusRegEntity::STATUS_CANCEL)
        );
    }

    /**
     * Create a variation
     *
     * @param array $ebsrData
     * @return BusRegEntity
     */
    private function createVar($ebsrData)
    {
        $busReg = $this->getRepo()->fetchLatestUsingRegNo($ebsrData['licNo'] . '/' . $ebsrData['routeNo']);

        return $busReg->createVariation(
            $this->getRepo()->getRefdataReference(BusRegEntity::STATUS_CANCEL),
            $this->getRepo()->getRefdataReference(BusRegEntity::STATUS_CANCEL)
        );
    }

    /**
     * @param array $ebsrData
     * @return array
     */
    private function processEbsrInformation(array $ebsrData)
    {
        $subsidised = (isset($ebsrData['subsidised']) ? $ebsrData['subsidised'] : BusRegEntity::SUBSIDY_NO);
        $ebsrData['subsidised'] = $this->getRepo()->getRefdataReference($subsidised);
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
     * @param null $serviceTypes
     * @return ArrayCollection
     */
    private function processServiceTypes($serviceTypes)
    {
        $result = new ArrayCollection();
        if (!empty($serviceTypes)) {
            foreach ($serviceTypes as $serviceType) {
                $result->add($this->getRepo()->getReference(BusServiceTypeEntity::class, $serviceType));
            }
        }
        return $result;
    }

    /**
     * @param array $serviceNumbers
     * @return array
     */
    private function processServiceNumbers(BusRegEntity $busReg, array $serviceNumbers)
    {
        foreach ($serviceNumbers as $number) {
            $otherServiceEntity = new BusRegOtherServiceEntity();
            $otherServiceEntity->setBusReg($busReg);
            $otherServiceEntity->setServiceNo($number);

            $this->getRepo('BusRegOtherService')->save($otherServiceEntity);
        }
    }

    /**
     * Returns collection of local authorities.
     *
     * @param array $localAuthority
     * @return ArrayCollection
     */
    private function processLocalAuthority($localAuthority)
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
    private function processTrafficAreas($trafficAreas)
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
