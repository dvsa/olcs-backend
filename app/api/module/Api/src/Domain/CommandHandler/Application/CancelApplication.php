<?php

/**
 * Cancel application
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Entity\Application\Application;

/**
 * Cancel application
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class CancelApplication extends AbstractCommandHandler
{
    public $repoServiceName = 'Application';

    public function handleCommand(CommandInterface $command)
    {
        /* @var $application Application */
        $application = $this->getRepo()->fetchById($command->getId());

        $application->setStatus($this->getRepo()->getRefdataReference(Application::APPLICATION_STATUS_CANCELLED));
        $this->getRepo()->save($application);

        $this->result->addMessage('Application cancelled');
        $this->result->addId('application', $application->getId());

        return $this->result;
    }
}
