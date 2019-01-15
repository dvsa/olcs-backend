<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\IrhpApplication;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\IrhpApplication\Update as UpdateCmd;

/**
 * Update checked answers information
 *
 * @author Tonci Vidovic <tonci.vidovic@capgemini.com>
 */
final class Update extends AbstractCommandHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_PERMITS];
    protected $repoServiceName = 'IrhpApplication';

    /**
     * Update the checked answers field
     *
     * @param CommandInterface $command
     *
     * @return Result
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    public function handleCommand(CommandInterface $command)
    {
        /**
         * @var IrhpApplication $application
         * @var UpdateCmd    $command
         */
        $application = $this->getRepo()->fetchById($command->getId());

        if (!empty($command->getCheckedAnswers())) {
            $application->setCheckedAnswers($command->getCheckedAnswers());
        }

        $this->getRepo()->save($application);

        $result = new Result();
        $result->addId('irhpApplication', $application->getId());
        $result->addMessage('IRHP application updated');

        return $result;
    }
}
