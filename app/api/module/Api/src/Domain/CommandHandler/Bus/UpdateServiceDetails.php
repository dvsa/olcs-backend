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
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Update Service Details
 */
final class UpdateServiceDetails extends AbstractCommandHandler
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
            $command->getReceivedDate(),
            $command->getEffectiveDate(),
            $command->getEndDate(),
            $busNoticePeriod,
            $busRules
        );

        try {

            $this->getRepo()->save($busReg);
            $result->addMessage('Saved successfully');
            return $result;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}
