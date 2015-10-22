<?php

/**
 * Process Ebsr packs
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Bus\Ebsr;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation as OrganisationEntity;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Ebsr\EbsrSubmission as EbsrSubmissionEntity;
use Dvsa\Olcs\Api\Entity\Bus\BusReg as BusRegEntity;
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
use Dvsa\Olcs\Api\Domain\TransExchangeAwareInterface;
use Dvsa\Olcs\Api\Domain\TransExchangeAwareTrait;
use Zend\ServiceManager\ServiceLocatorInterface;
use Doctrine\ORM\Query;

/**
 * Process Ebsr packs
 */
final class ProcessPacks extends AbstractCommandHandler
    implements AuthAwareInterface, TransactionedInterface, UploaderAwareInterface, TransExchangeAwareInterface
{
    use AuthAwareTrait;
    use UploaderAwareTrait;
    use TransExchangeAwareTrait;

    protected $repoServiceName = 'bus';

    protected $extraRepos = ['Document', 'EbsrSubmission', 'Licence', 'BusRegOtherService'];

    /**
     * @var
     */
    protected $fileStructure;

    protected $xmlStructure;

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $mainServiceLocator = $serviceLocator->getServiceLocator();

        $this->fileStructure = $mainServiceLocator->get('EbsrFileStructure');
        $this->xmlStructure = $mainServiceLocator->get('EbsrXmlStructure');

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

            $file = $this->getUploader()->download($document->getIdentifier());
            $xmlFilename = $this->fileStructure->getValue($file);
            $ebsrData = $this->xmlStructure->getValue($xmlFilename);

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

            $result->merge($this->handleSideEffects($sideEffects));
        }

        return $result;
    }

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

        $subsidised = (isset($ebsrData['subsidised']) ? $ebsrData['subsidised'] : BusRegEntity::SUBSIDY_NO);

        $busReg = BusRegEntity::createNew(
            $licence,
            $this->getRepo()->getRefdataReference(BusRegEntity::STATUS_NEW),
            $this->getRepo()->getRefdataReference(BusRegEntity::STATUS_NEW),
            $this->getRepo()->getRefdataReference($subsidised),
            $this->getRepo()->getReference(BusNoticePeriodEntity::class, BusNoticePeriodEntity::NOTICE_PERIOD_OTHER),
            'Y'
        );

        $busReg = $this->addEbsrInformation($busReg, $ebsrData);
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

        $busReg->createVariation(
            $this->getRepo()->getRefdataReference(BusRegEntity::STATUS_CANCEL),
            $this->getRepo()->getRefdataReference(BusRegEntity::STATUS_CANCEL)
        );

        $busReg = $this->addEbsrInformation($busReg, $ebsrData);
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

        $busReg->createVariation(
            $this->getRepo()->getRefdataReference(BusRegEntity::STATUS_CANCEL),
            $this->getRepo()->getRefdataReference(BusRegEntity::STATUS_CANCEL)
        );

        $busReg = $this->addEbsrInformation($busReg, $ebsrData);
    }

    private function addEbsrInformation(BusRegEntity $busReg, array $ebsrData)
    {
        $la = $this->processLocalAuthority($ebsrData['localAuthorities']);
        $busReg->setLocalAuthoritys($la);

        $ta = $this->processTrafficAreas($ebsrData['trafficAreas']);
        $busReg->setTrafficAreas($ta);

        $serviceTypes = $this->processServiceTypes($ebsrData['serviceClassifications']);
        $busReg->setBusServiceTypes($serviceTypes);

        $this->processServiceNumbers($busReg, $ebsrData['serviceNumbers']);
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
        //makes it easier to find keys we're removing
        $flipServiceNumbers = array_flip($serviceNumbers);

        //numbers we're removing
        /**  @var BusRegOtherServiceEntity $otherServiceEntity */
        foreach ($busReg->getOtherServices() as $otherServiceEntity) {
            if (!isset($flipServiceNumbers[$otherServiceEntity->getServiceNo()])) {
                $this->getRepo('BusRegOtherService')->delete($otherServiceEntity);
            }
        }

        //check for elements we already have
        //@todo do this more accurately
        foreach ($serviceNumbers as  $key => $number) {
            if ($busReg->getOtherServices()->contains($number)) {
                unset ($serviceNumbers[$key]);
            }
        }

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
            foreach ($localAuthority as $la) {
                $result->add($this->getRepo()->getReference(LocalAuthorityEntity::class, $la));
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
            foreach ($trafficAreas as $ta) {
                $result->add($this->getRepo()->getReference(TrafficAreaEntity::class, $ta));
            }
        }
        return $result;
    }
}
