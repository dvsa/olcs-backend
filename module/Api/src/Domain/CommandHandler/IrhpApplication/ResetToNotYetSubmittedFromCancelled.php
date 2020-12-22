<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\IrhpApplication;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity\IrhpInterface;
use Dvsa\Olcs\Api\Service\Permits\Fees\EcmtApplicationFeeCommandCreator;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\IrhpApplication\ResetToNotYetSubmittedFromCancelled
    as ResetToNotYetSubmittedFromCancelledCmd;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Reset to not yet submitted from cancelled
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class ResetToNotYetSubmittedFromCancelled extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'IrhpApplication';

    /** @var EcmtApplicationFeeCommandCreator */
    private $ecmtApplicationFeeCommandCreator;

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator Service Manager
     *
     * @return $this
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $mainServiceLocator = $serviceLocator->getServiceLocator();

        $this->ecmtApplicationFeeCommandCreator = $mainServiceLocator->get('PermitsFeesEcmtApplicationFeeCommandCreator');

        return parent::createService($serviceLocator);
    }

    /**
     * Handle command
     *
     * @param ResetToNotYetSubmittedFromCancelledCmd|CommandInterface $command command
     *
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        $irhpApplicationId = $command->getId();
        $irhpApplication = $this->getRepo()->fetchById($irhpApplicationId);

        $irhpApplication->resetToNotYetSubmittedFromCancelled(
            $this->refData(IrhpInterface::STATUS_NOT_YET_SUBMITTED)
        );

        $permitsRequired = $irhpApplication->getFirstIrhpPermitApplication()
            ->getTotalEmissionsCategoryPermitsRequired();

        $this->handleSideEffect(
            $this->ecmtApplicationFeeCommandCreator->create($irhpApplication, $permitsRequired)
        );

        $this->getRepo()->save($irhpApplication);

        $this->result->addMessage('IRHP application reset to not yet submitted');
        $this->result->addId('irhpApplication', $irhpApplicationId);
        return $this->result;
    }
}
