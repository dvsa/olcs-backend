<?php

/**
 * Create Bus Reg Fee
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Bus;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Fee\CreateFee;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Entity\Bus\BusReg;
use Dvsa\Olcs\Api\Entity\Fee\Fee as FeeEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Domain\Repository\FeeType;
use Dvsa\Olcs\Api\Entity\Fee\FeeType as FeeTypeEntity;

/**
 * Create BusReg Fee
 */
final class CreateBusRegFee extends AbstractCommandHandler implements AuthAwareInterface
{
    use AuthAwareTrait;

    /**
     * @var FeeType
     */
    protected $feeTypeRepo;

    protected $repoServiceName = 'Bus';

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->feeTypeRepo = $serviceLocator->getServiceLocator()->get('RepositoryServiceManager')
            ->get('FeeType');

        return parent::createService($serviceLocator);
    }

    public function handleCommand(CommandInterface $command)
    {
        /** @var BusReg $bus */
        $busReg = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT);
        $trafficArea = $busReg->getLicence()->getTrafficArea();

        if ($busReg->getVariationNo()) {
            $feeType = FeeTypeEntity::FEE_TYPE_BUSVAR;
        } else {
            $feeType = FeeTypeEntity::FEE_TYPE_BUSAPP;
        }

        $feeType = $this->getRepo()->getRefdataReference(FeeTypeEntity::FEE_TYPE_APP);

        $feeTrafficArea = null;

        if ($trafficArea->getIsScotland() === 'Y') {
            $feeTrafficArea = $trafficArea->getId();
        }

        $date = new \DateTime($busReg->getReceivedDate());

        $feeType = $this->feeTypeRepo->fetchLatest(
            $feeType,
            LicenceEntity::LICENCE_CATEGORY_PSV,
            $busReg->getLicence()->getLicenceType()->getId(),
            $date,
            $trafficArea
        );

        $data = [
            'busReg' => $busReg->getId(),
            'licence' => $busReg->getLicence()->getId(),
            'invoicedDate' => new \DateTime($busReg->getReceivedDate()),
            'description' => $feeType->getDescription() . ' ' . $busReg->getRegNo() . ' ' . $busReg->getId(),
            'feeType' => $feeType->getId(),
            'feeStatus' => FeeEntity::STATUS_OUTSTANDING,
            'amount' => $feeType->getFixedValue() == 0 ? $feeType->getFiveYearValue() : $feeType->getFixedValue()
        ];

        return CreateFee::create($data);

        try {
            $result = new Result();

            $this->getRepo()->beginTransaction();

            $this->getRepo()->commit();

            return $result;
        } catch (\Exception $ex) {
            $this->getRepo()->rollback();

            throw $ex;
        }
    }
}
