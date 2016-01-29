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

        if ($trafficArea->getIsScotland()) {
            $feeTrafficArea = TrafficAreaEntity::SCOTTISH_TRAFFIC_AREA_CODE;
        }

        $receivedDate = $busReg->getReceivedDate();

        if (!$receivedDate instanceof \DateTime) {
            $receivedDate = new \DateTime($receivedDate);
        }

        $feeType = $this->feeTypeRepo->fetchLatest(
            $feeType,
            $busReg->getLicence()->getGoodsOrPsv(),
            $busReg->getLicence()->getLicenceType(),
            $receivedDate,
            $feeTrafficArea
        );

        $data = [
            'busReg' => $busReg->getId(),
            'licence' => $busReg->getLicence()->getId(),
            'invoicedDate' => $receivedDate->format('Y-m-d'),
            'description' => $feeType->getDescription() . ' ' . $busReg->getRegNo() . ' V' . $busReg->getVariationNo(),
            'feeType' => $feeType->getId(),
            'feeStatus' => FeeEntity::STATUS_OUTSTANDING,
            'amount' => $feeType->getFixedValue()
        ];

        return $this->handleSideEffect(CreateFee::create($data));
    }
}
