<?php

/**
 * Cancel All Interim Fees
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use \Dvsa\Olcs\Api\Domain\Repository\Fee as FeeRepo;
use Dvsa\Olcs\Api\Domain\Command\Fee\CancelFee;

/**
 * Cancel All Interim Fees
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
final class CancelAllInterimFees extends AbstractCommandHandler
{
    /**
     * @var FeeRepo
     */
    protected $feeRepo;

    protected $repoServiceName = 'Application';

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->feeRepo = $serviceLocator->getServiceLocator()->get('RepositoryServiceManager')
            ->get('Fee');

        return parent::createService($serviceLocator);
    }

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        try {
            $this->getRepo()->beginTransaction();

            $fees = $this->feeRepo->fetchInterimFeesByApplicationId($command->getId(), true);

            /* @var $fee \Dvsa\Olcs\Api\Entity\Fee\Fee */
            foreach ($fees as $fee) {
                $result->merge(
                    $this->getCommandHandler()->handleCommand(
                        CancelFee::create(['id' => $fee->getId()])
                    )
                );
            }

            $this->getRepo()->commit();

            $result->addMessage('CancelAllInterimFees success');
            return $result;
        } catch (\Exception $ex) {
            $this->getRepo()->rollback();

            throw $ex;
        }
    }
}
