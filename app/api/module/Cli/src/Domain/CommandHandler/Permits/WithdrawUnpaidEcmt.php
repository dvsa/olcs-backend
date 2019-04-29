<?php

namespace Dvsa\Olcs\Cli\Domain\CommandHandler\Permits;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Cli\Domain\Command\Permits\WithdrawUnpaidEcmt as WithdrawUnpaidEcmtCmd;
use Dvsa\Olcs\Api\Domain\Repository\EcmtPermitApplication as EcmtPermitApplicationRepo;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Transfer\Command\Permits\WithdrawEcmtPermitApplication;
use Dvsa\Olcs\Transfer\Query\Permits\EcmtPermitApplication as EcmtAppQuery;

/**
 * Withdraw ECMT applications that haven't been paid in time
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
final class WithdrawUnpaidEcmt extends AbstractCommandHandler implements TransactionedInterface, ToggleRequiredInterface
{
    use ToggleAwareTrait;

    private $query;
    protected $toggleConfig = [FeatureToggle::BACKEND_PERMITS];
    protected $repoServiceName = 'EcmtPermitApplication';

    /**
     * Handle command
     *
     * @param CommandInterface $command command
     *
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        /**
         * @var EcmtPermitApplicationRepo $repo
         * @var WithdrawUnpaidEcmtCmd     $command
         */
        $repo = $this->getRepo();

        $queryData = ['statusIds' => [EcmtPermitApplication::STATUS_AWAITING_FEE]];
        $this->query = EcmtAppQuery::create($queryData);
        $ecmtApps = $repo->fetchList($this->query, Query::HYDRATE_OBJECT);

        /** @var EcmtPermitApplication $application */
        foreach ($ecmtApps as $application) {
            if ($application->issueFeeOverdue()) {
                $cmdData = [
                    'id' => $application->getId(),
                    'reason' => EcmtPermitApplication::WITHDRAWN_REASON_UNPAID
                ];

                $withdrawCmd = WithdrawEcmtPermitApplication::create($cmdData);

                $this->result->merge(
                    $this->handleSideEffect($withdrawCmd)
                );
            }
        }

        return $this->result;
    }

    /**
     * Used for UT only, to test the correct query is being passed
     */
    public function getQuery()
    {
        return $this->query;
    }
}
