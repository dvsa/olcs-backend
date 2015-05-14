<?php

/**
 * Reset Application
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Exception;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Entity\Application\Application;

/**
 * Reset Application
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class ResetApplication extends AbstractCommandHandler
{
    protected $repoServiceName = 'Application';

    public function handleCommand(CommandInterface $command)
    {
        /** @var Application $application */
        $application = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT);

        if ($command->getConfirm() === false) {
            // Before we tell the UI we need confirmation, we better validate the values
            $application->validate(
                $command->getNiFlag(),
                $this->getRepo()->getRefdataReference($command->getOperatorType()),
                $this->getRepo()->getRefdataReference($command->getLicenceType())
            );

            // Tell the UI we need confirmation
            throw new Exception\RequiresConfirmationException(
                'Updating these elements requires confirmation',
                Application::ERROR_REQUIRES_CONFIRMATION
            );
        }

        $this->getRepo()->delete($application);
    }
}
