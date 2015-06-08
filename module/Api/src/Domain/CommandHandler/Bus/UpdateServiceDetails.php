<?php

/**
 * Update Service Details
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Bus;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Entity\Bus\BusReg;
use Dvsa\Olcs\Api\Entity\Bus\BusNoticePeriod;
use Dvsa\Olcs\Transfer\Command\Bus\UpdateServiceDetails as UpdateServiceDetailsCmd;
use Dvsa\Olcs\Api\Domain\Repository\Fee;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Domain\Command\Bus\CreateBusFee as CmdCreateBusFee;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;

/**
 * Update Service Details
 */
final class UpdateServiceDetails extends AbstractCommandHandler implements TransactionedInterface
{
    /**
     * @var BusNoticePeriod
     */
    protected $busNoticePeriodRepo;

    /**
     * @var Fee
     */
    protected $feeRepo;

    protected $repoServiceName = 'Bus';

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->busNoticePeriodRepo = $serviceLocator->getServiceLocator()->get('RepositoryServiceManager')
            ->get('BusNoticePeriod');

        $this->feeRepo = $serviceLocator->getServiceLocator()->get('RepositoryServiceManager')
            ->get('Fee');

        return parent::createService($serviceLocator);
    }

    /**
     * @param CommandInterface $command
     * @return Result
     * @throws \Exception
     */
    public function handleCommand(CommandInterface $command)
    {
        /** @var UpdateServiceDetailsCmd $command */
        /** @var BusReg $busReg */

        $result = new Result();

        $busReg = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT, $command->getVersion());
        $busRegId = $busReg->getId();
        $busNoticePeriod = $command->getBusNoticePeriod();

        //short notice rules
        $busRules = false;

        if ($busNoticePeriod) {
            $busRules = $this->busNoticePeriodRepo->fetchById($busNoticePeriod, Query::HYDRATE_OBJECT);
        }

        $busReg->updateServiceDetails(
            $command->getServiceNo(),
            $command->getOtherServices(),
            $command->getStartPoint(),
            $command->getFinishPoint(),
            $command->getVia(),
            $command->getBusServiceTypes(),
            $command->getOtherDetails(),
            $command->getReceivedDate(),
            $command->getEffectiveDate(),
            $command->getEndDate(),
            $busNoticePeriod,
            $busRules
        );

        $this->getRepo()->save($busReg);

        if ($this->shouldCreateFee($busRegId)) {
            $result->merge($this->getCommandHandler()->handleCommand($this->createBusFeeCommand($busRegId)));
        }

        $result->addMessage('Saved successfully');
    }

    /**
     * Returns whether we should create a fee
     * (basically this is down to whether there's already a fee in place for this busReg(
     *
     * @param $busRegId
     * @return bool
     */
    private function shouldCreateFee($busRegId)
    {
        $latestFee = $this->feeRepo->getLatestFeeForBusReg($busRegId);

        if (!empty($latestFee)) {
            return false;
        }

        return true;
    }

    /**
     * @param $busRegId
     * @return Result
     */
    private function createBusFeeCommand($busRegId)
    {
        return $this->getCommandHandler()->handleCommand(CmdCreateBusFee::create(['id' => $busRegId]));
    }
}
