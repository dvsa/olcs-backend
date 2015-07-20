<?php

/**
 * Create Bus Reg Fee
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Bus;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Fee\CreateFee;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Entity\Bus\BusReg;
use Dvsa\Olcs\Api\Entity\Fee\Fee as FeeEntity;
use Dvsa\Olcs\Api\Domain\Repository\FeeType;
use Dvsa\Olcs\Api\Entity\Fee\FeeType as FeeTypeEntity;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea as TrafficAreaEntity;

/**
 * Create BusReg Fee
 */
final class CreateBusFee extends AbstractCommandHandler
{

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
        /** @var BusReg $busReg */
        $busReg = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT);
        $trafficArea = $busReg->getLicence()->getTrafficArea();

        if ($busReg->getVariationNo()) {
            $feeType = FeeTypeEntity::FEE_TYPE_BUSVAR;
        } else {
            $feeType = FeeTypeEntity::FEE_TYPE_BUSAPP;
        }

        $feeType = $this->feeTypeRepo->getRefdataReference($feeType);

        $feeTrafficArea = null;

        if ($trafficArea->getIsScotland() === 'Y') {
            $feeTrafficArea = $this->getRepo()->getReference(
                TrafficAreaEntity::class,
                TrafficAreaEntity::SCOTTISH_TRAFFIC_AREA_CODE
            );
        }

        $feeType = $this->feeTypeRepo->fetchLatest(
            $feeType,
            $busReg->getLicence()->getGoodsOrPsv(),
            $busReg->getLicence()->getLicenceType(),
            $busReg->getReceivedDate(),
            $feeTrafficArea
        );

        $invoicedDate
            = ($busReg->getReceivedDate() instanceof \DateTime) ? $busReg->getReceivedDate()->format('Y-m-d') : null;

        $data = [
            'busReg' => $busReg->getId(),
            'licence' => $busReg->getLicence()->getId(),
            'invoicedDate' => $invoicedDate,
            'description' => $feeType->getDescription() . ' ' . $busReg->getRegNo() . ' ' . $busReg->getId(),
            'feeType' => $feeType->getId(),
            'feeStatus' => FeeEntity::STATUS_OUTSTANDING,
            'amount' => $feeType->getFixedValue()
        ];

        return CreateFee::create($data);
    }
}
