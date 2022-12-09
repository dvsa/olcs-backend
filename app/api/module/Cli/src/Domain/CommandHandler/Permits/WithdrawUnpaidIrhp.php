<?php

namespace Dvsa\Olcs\Cli\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\WithdrawableInterface;
use Dvsa\Olcs\Api\Service\Permits\Fees\DaysToPayIssueFeeProvider;
use Dvsa\Olcs\Cli\Domain\Command\Permits\WithdrawUnpaidIrhp as WithdrawUnpaidIrhpCmd;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\IrhpApplication\Withdraw as WithdrawCmd;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Withdraw IRHP applications that haven't been paid in time
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
final class WithdrawUnpaidIrhp extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'IrhpApplication';

    /** @var DaysToPayIssueFeeProvider */
    private $daysToPayIssueFeeProvider;

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator Service Manager
     *
     * @return $this
     */
    public function createService(ServiceLocatorInterface $serviceLocator, $name = null, $requestedName = null)
    {
        $mainServiceLocator = $serviceLocator->getServiceLocator();

        $this->daysToPayIssueFeeProvider = $mainServiceLocator->get('PermitsFeesDaysToPayIssueFeeProvider');

        return parent::createService($serviceLocator);
    }

    /**
     * Handle command
     *
     * @param CommandInterface|WithdrawUnpaidIrhpCmd $command command
     *
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        $irhpApplications = $this->getRepo()->fetchAllAwaitingFee();
        $daysToPayIssueFee = $this->daysToPayIssueFeeProvider->getDays();

        foreach ($irhpApplications as $irhpApplication) {
            if ($irhpApplication->issueFeeOverdue($daysToPayIssueFee)) {
                $withdrawCmd = WithdrawCmd::create(
                    [
                        'id' => $irhpApplication->getId(),
                        'reason' => WithdrawableInterface::WITHDRAWN_REASON_UNPAID,
                    ]
                );

                $this->result->merge(
                    $this->handleSideEffect($withdrawCmd)
                );
            }
        }

        return $this->result;
    }
}
