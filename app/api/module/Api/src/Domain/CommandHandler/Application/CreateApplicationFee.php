<?php

/**
 * Create Application Fee
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Fee\CreateFee;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Command\Task\CreateTask;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Application\CreateApplicationFee as Cmd;
use Dvsa\Olcs\Api\Entity\Task\Task;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Domain\Repository\FeeType;
use Dvsa\Olcs\Api\Entity\Fee\FeeType as FeeTypeEntity;

/**
 * Create Application Fee
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class CreateApplicationFee extends AbstractCommandHandler
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

    public function handleCommand(CommandInterface $command)
    {
        try {
            $result = new Result();

            $this->getRepo()->beginTransaction();

            $taskId = null;

            if ($this->shouldCreateTask()) {

                $taskResult = $this->getCommandHandler()->handleCommand($this->createCreateTaskCommand($command));
                $result->merge($taskResult);

                $taskId = $taskResult->getId('task');
            }

            $result->merge($this->getCommandHandler()->handleCommand($this->createCreateFeeCommand($command, $taskId)));

            $this->getRepo()->commit();

            return $result;
        } catch (\Exception $ex) {
            $this->getRepo()->rollback();

            throw $ex;
        }
    }

    /**
     * @param Cmd $command
     * @return CreateTask
     */
    private function createCreateTaskCommand(Cmd $command)
    {
        /** @var Application $application */
        $application = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT);

        $currentUser = $this->fetchCurrentUser();

        $data = [
            'category' => Task::CATEGORY_APPLICATION,
            'subCategory' => Task::SUBCATEGORY_FEE_DUE,
            'description' => 'Application Fee Due',
            'actionDate' => date('Y-m-d'),
            'assignedToUser' => $currentUser['id'],
            'assignedToTeam' => $currentUser['team']['id'],
            'application' => $application->getId(),
            'licence' => $application->getLicence()->getId()
        ];

        return CreateTask::create($data);
    }

    /**
     * @param Cmd $command
     * @param $taskId
     * @return CreateFee
     * @throws \Dvsa\Olcs\Api\Domain\Exception\NotFoundException
     */
    private function createCreateFeeCommand(Cmd $command, $taskId)
    {
        /** @var Application $application */
        $feeType = $this->getRepo()->getRefdataReference(FeeTypeEntity::FEE_TYPE_APP);
        $application = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT);
        $trafficArea = $application->getNiFlag() === 'Y'
            ? $this->getRepo()->getReference(TrafficArea::class, TrafficArea::NORTHERN_IRELAND_TRAFFIC_AREA_CODE)
            : null;

        $date = $application->getReceivedDate() === null
            ? $application->getCreatedOn()
            : $application->getReceivedDate();

        if (is_string($date)) {
            $date = new \DateTime($date);
        }

        $feeType = $this->feeTypeRepo->fetchLatest(
            $feeType,
            $application->getGoodsOrPsv(),
            $application->getLicenceType(),
            $date,
            $trafficArea
        );

        $data = [
            'task' => $taskId,
            'application' => $application->getId(),
            'licence' => $application->getLicence()->getId(),
            'invoicedDate' => date('Y-m-d'),
            'description' => $feeType->getDescription() . ' for application ' . $application->getId(),
            'feeType' => $feeType->getId(),
            'amount' => $feeType->getFixedValue() == 0 ? $feeType->getFiveYearValue() : $feeType->getFixedValue()
        ];

        return CreateFee::create($data);
    }

    /**
     * Check whether we need to create a task
     *
     * @return boolean
     */
    private function shouldCreateTask()
    {
        return $this->isGranted('internal-view');
    }

    /**
     * @TODO Need to replace this when we have auth
     *
     * @return array
     */
    private function fetchCurrentUser()
    {
        return [
            'id' => 1,
            'team' => [
                'id' => 2
            ]
        ];
    }

    /**
     * @TODO Need to replace this with a real way to determine between internal and selfserve users
     *
     * @param $permission
     * @return bool
     */
    private function isGranted($permission)
    {
        return true;
    }
}
