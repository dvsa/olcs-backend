<?php

/**
 * CreateFee
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Domain\Repository\FeeType;

/**
 * CreateFee
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
final class CreateFee extends AbstractCommandHandler
{
    /**
     * @var FeeType
     */
    protected $feeTypeRepo;

    protected $repoServiceName = 'Application';

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->feeTypeRepo = $serviceLocator->getServiceLocator()->get('RepositoryServiceManager')
            ->get('FeeType');

        return parent::createService($serviceLocator);
    }

    /**
     * @param \Dvsa\Olcs\Api\Domain\Command\Application\CreateFee $command
     *
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();
        $result->merge($this->getCommandHandler()->handleCommand($this->createCreateFeeCommand($command)));

        return $result;
    }

    /**
     * Create the createFee command
     *
     * @param \Dvsa\Olcs\Api\Domain\Command\Application\CreateFee $command
     *
     * @return \Dvsa\Olcs\Api\Domain\Command\Fee\CreateFee
     */
    private function createCreateFeeCommand(\Dvsa\Olcs\Api\Domain\Command\Application\CreateFee $command)
    {
        /** @var Application $application */
        $application = $this->getRepo()->fetchUsingId($command);

        $trafficArea = null;
        if ($application->getNiFlag() === 'Y') {
            $trafficArea = $this->getRepo()
                ->getReference(TrafficArea::class, TrafficArea::NORTHERN_IRELAND_TRAFFIC_AREA_CODE);
        }

        $date = $application->getReceivedDate();
        if ($date === null) {
            $date = $application->getCreatedOn();
        }

        if (is_string($date)) {
            $date = new \DateTime($date);
        }

        $feeType = $this->feeTypeRepo->fetchLatest(
            $this->getRepo()->getRefdataReference($command->getFeeTypeFeeType()),
            $application->getGoodsOrPsv(),
            $application->getLicenceType(),
            $date,
            $trafficArea
        );

        $data = [
            'application' => $application->getId(),
            'licence' => $application->getLicence()->getId(),
            'invoicedDate' => date('Y-m-d'),
            'description' => $feeType->getDescription() . ' for application ' . $application->getId(),
            'feeType' => $feeType->getId(),
            'amount' => $feeType->getFixedValue() == 0 ? $feeType->getFiveYearValue() : $feeType->getFixedValue()
        ];

        return \Dvsa\Olcs\Api\Domain\Command\Fee\CreateFee::create($data);
    }
}
