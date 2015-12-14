<?php

/**
 * Update Service Details
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Bus;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Doctrine\ORM\Query;
use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Entity\Bus\BusReg;
use Dvsa\Olcs\Api\Entity\Bus\BusNoticePeriod as BusNoticePeriodEntity;
use Dvsa\Olcs\Api\Entity\Bus\BusRegOtherService;
use Dvsa\Olcs\Api\Entity\Bus\BusServiceType as BusServiceTypeEntity;
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

    protected $extraRepos = ['BusRegOtherService'];

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
            $command->getStartPoint(),
            $command->getFinishPoint(),
            $command->getVia(),
            $command->getOtherDetails(),
            $command->getReceivedDate(),
            $command->getEffectiveDate(),
            $command->getEndDate(),
            $this->getRepo()->getReference(BusNoticePeriodEntity::class, $busNoticePeriod),
            $busRules
        );

        $serviceTypes = $this->processServiceTypes($command->getBusServiceTypes());
        $busReg->setBusServiceTypes($serviceTypes);

        $this->getRepo()->save($busReg);

        $this->processServiceNumbers($busReg, $command->getOtherServices());

        if ($busReg->getReceivedDate() !== null && $this->shouldCreateFee($busRegId)) {
            $result->merge($this->handleSideEffect($this->createBusFeeCommand($busRegId)));
        }

        $result->addId('BusReg', $busRegId);
        $result->addMessage('Bus registration saved successfully');

        return $result;
    }

    /**
     * Returns whether we should create a fee
     * (basically this is down to whether there's already a fee in place for this busReg)
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
     * @param array $otherServiceNumbers
     * @return array
     */
    private function processServiceNumbers(BusReg $busReg, array $otherServiceNumbers)
    {
        $reduced = [];

        if (!empty($otherServiceNumbers)) {
            foreach ($otherServiceNumbers as $serviceNumber) {
                if (empty($serviceNumber['serviceNo'])) {
                    // filter out empty values
                    continue;
                }

                if (!empty($serviceNumber['id'])) {
                    // update
                    /** @var BusRegOtherService $otherServiceEntity */
                    $otherServiceEntity = $this->getRepo('BusRegOtherService')->fetchById(
                        $serviceNumber['id'],
                        Query::HYDRATE_OBJECT,
                        $serviceNumber['version']
                    );
                    $otherServiceEntity->setServiceNo($serviceNumber['serviceNo']);
                } else {
                    // create
                    $otherServiceEntity = new BusRegOtherService($busReg, $serviceNumber['serviceNo']);
                }

                $this->getRepo('BusRegOtherService')->save($otherServiceEntity);
                $reduced[] = $otherServiceEntity->getId();
            }

            // remove the remaining records
            foreach ($busReg->getOtherServices() as $otherServiceEntity) {
                if (!in_array($otherServiceEntity->getId(), $reduced)) {
                    $this->getRepo('BusRegOtherService')->delete($otherServiceEntity);
                }
            }
        }

        return $reduced;
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
     * @param $busRegId
     * @return CmdCreateBusFee
     */
    private function createBusFeeCommand($busRegId)
    {
        return CmdCreateBusFee::create(['id' => $busRegId]);
    }
}
