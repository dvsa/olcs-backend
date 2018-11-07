<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Domain\QueueAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\Fee\Fee;
use Dvsa\Olcs\Api\Entity\Fee\FeeType;
use Dvsa\Olcs\Api\Entity\Queue\Queue;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\Permits\AcceptEcmtPermits as AcceptEcmtPermitsCmd;
use Dvsa\Olcs\Transfer\Command\Permits\CompleteIssuePayment;

/**
 * Accept an awarded ECMT Permit application
 *
 * @author Tonci Vidovic <tonci.vidovic@capgemini.com>
 */
final class AcceptEcmtPermits extends AbstractCommandHandler implements ToggleRequiredInterface
{
    use QueueAwareTrait;
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_ECMT];
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
         * @var EcmtPermitApplication $application
         * @var AcceptEcmtPermitsCmd  $command
         */

        $ecmtPermitApplicationId = $command->getId();
        $application = $this->getRepo()->fetchById($ecmtPermitApplicationId);

        // If the application is in AwaitingFee status, but has no outstanding IssueFees (e.g. after internal Cash payment)
        // Call command to set correct status before continuing with Permit Acceptance.
        if (!$this->hasOutstandingIssueFees($application->getFees()) && $application->isAwaitingFee()) {
            $this->result->merge($this->handleSideEffect(CompleteIssuePayment::create(['id' => $application->getId()])));
        }

        $result = new Result();
        $result->addId('ecmtPermitApplication', $ecmtPermitApplicationId);

        $newStatus = $this->refData(EcmtPermitApplication::STATUS_ISSUING);
        try {
            $application->proceedToIssuing($newStatus);
        } catch (ForbiddenException $e) {
            $result->addMessage('Unable to issue permit for application');
            return $result;
        }

        $this->getRepo()->save($application);
        $result->addMessage('Queuing issue of application permits');
        $allocateCmd = $this->createQueue($ecmtPermitApplicationId, Queue::TYPE_PERMITS_ALLOCATE, []);
        $result->merge(
            $this->handleSideEffect($allocateCmd)
        );

        return $result;
    }

    /**
     * check for and return an outstanding free from the ['fees'] array on the permit application entity
     *
     * @param $fees
     * @return bool
     */
    protected function hasOutstandingIssueFees($fees)
    {
        foreach ($fees as $key => $fee) {
            if ($fee->getFeeStatus()->getId() === Fee::STATUS_OUTSTANDING
                && $fee->getFeeType()->getFeeType()->getId() === FeeType::FEE_TYPE_ECMT_ISSUE) {
                return true;
            }
        }
        return false;
    }
}
