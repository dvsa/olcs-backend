<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Domain\QueueAwareTrait;
use Dvsa\Olcs\Api\Entity\Queue\Queue;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\Permits\AcceptEcmtPermits as AcceptEcmtPermitsCmd;

/**
 * Accept an awarded ECMT Permit application
 *
 * @author Tonci Vidovic <tonci.vidovic@capgemini.com>
 */
final class AcceptEcmtPermits extends AbstractCommandHandler
{
    use QueueAwareTrait;

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
}
