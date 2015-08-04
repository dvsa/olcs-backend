<?php

/**
 * Delete Operating Centres
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\Application\ApplicationOperatingCentre;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\Application\DeleteOperatingCentres as Cmd;

/**
 * Delete Operating Centres
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class DeleteOperatingCentres extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Application';

    protected $extraRepos = ['ApplicationOperatingCentre'];

    /**
     * @param Cmd $command
     * @return \Dvsa\Olcs\Api\Domain\Command\Result
     */
    public function handleCommand(CommandInterface $command)
    {
        /** @var ApplicationEntity $application */
        $application = $this->getRepo()->fetchById($command->getApplication());

        $aocs = $application->getOperatingCentres();

        $count = 0;

        /** @var ApplicationOperatingCentre $aoc */
        foreach ($aocs as $aoc) {
            if (in_array($aoc->getId(), $command->getIds())) {
                $count++;
                $this->getRepo('ApplicationOperatingCentre')->delete($aoc);
                $aocs->removeElement($aoc);
            }
        }

        $this->result->addMessage($count . ' Operating Centre(s) removed');

        if ($aocs->count() === 0) {
            $licence = $application->getLicence();

            $licence->setEnforcementArea(null);
            $licence->setTrafficArea(null);

            $this->getRepo()->save($application);

            $this->result->addMessage('Updated traffic area');
            $this->result->addMessage('Updated enforcement area');
        }

        $this->result->merge(
            $this->handleSideEffect(
                \Dvsa\Olcs\Api\Domain\Command\Application\UpdateApplicationCompletion::create(
                    [
                        'id' => $application->getId(),
                        'section' => 'operatingCentres'
                    ]
                )
            )
        );

        return $this->result;
    }
}
