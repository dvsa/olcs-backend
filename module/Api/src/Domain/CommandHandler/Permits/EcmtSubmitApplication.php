<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\Command\Email\SendEcmtAppSubmitted as SendEcmtAppSubmittedCmd;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Domain\QueueAwareTrait;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\Permits\EcmtSubmitApplication as EcmtSubmitApplicationCmd;

/**
 * Submit the ECMT application
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
final class EcmtSubmitApplication extends AbstractCommandHandler
{
    use QueueAwareTrait;

    protected $repoServiceName = 'EcmtPermitApplication';

    /**
     * Submit the ECMT application
     *
     * @param CommandInterface $command
     *
     * @return Result
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     * @throws ForbiddenException
     */
    public function handleCommand(CommandInterface $command)
    {
        /**
         * @var EcmtPermitApplication       $application
         * @var EcmtSubmitApplicationCmd    $command
         */
        $id = $command->getId();
        $newStatus = $this->getRepo()->getRefdataReference(EcmtPermitApplication::STATUS_UNDER_CONSIDERATION);
        $application = $this->getRepo()->fetchById($id);
        $application->submit($newStatus);

        $this->getRepo()->save($application);

        $result = new Result();
        $result->addId('ecmtPermitApplication', $id);
        $result->addMessage('Permit application updated');

        $emailCmd = $this->emailQueue(SendEcmtAppSubmittedCmd::class, ['id' => $id], $id);

        //queue the email confirming submission
        $result->merge(
            $this->handleSideEffect($emailCmd)
        );

        return $result;
    }
}
