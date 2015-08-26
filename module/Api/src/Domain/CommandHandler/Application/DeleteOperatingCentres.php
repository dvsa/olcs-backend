<?php

/**
 * Delete Operating Centres
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Api\Domain\Command\OperatingCentre\DeleteApplicationLinks;
use Dvsa\Olcs\Api\Domain\Command\OperatingCentre\DeleteConditionUndertakings;
use Dvsa\Olcs\Api\Domain\Command\OperatingCentre\DeleteTmLinks;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\Application\ApplicationOperatingCentre;
use Dvsa\Olcs\Transfer\Command\Application\DeleteOperatingCentres as Cmd;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

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
                $message = $aoc->checkCanDelete();
                if ($message) {
                    throw new \Dvsa\Olcs\Api\Domain\Exception\BadRequestException(key($message));
                }
                $count++;
                $this->getRepo('ApplicationOperatingCentre')->delete($aoc);
                $aocs->removeElement($aoc);
                $this->result->merge($this->deleteConditionUndertakings($aoc));
                $this->result->merge($this->deleteTransportManagerLinks($aoc));
                $this->result->merge($this->deleteFromOtherApplications($aoc));
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

    /**
     * @param ApplicationOperatingCentre $laoc
     * @return Result
     */
    private function deleteConditionUndertakings($aoc)
    {
        return $this->handleSideEffect(
            DeleteConditionUndertakings::create(
                [
                    'operatingCentre' => $aoc->getOperatingCentre(),
                    'application' => $aoc->getApplication(),
                ]
            )
        );
    }

    private function deleteTransportManagerLinks($aoc)
    {
        return $this->handleSideEffect(
            DeleteTmLinks::create(['operatingCentre' => $aoc->getOperatingCentre()])
        );
    }

    private function deleteFromOtherApplications($aoc)
    {
        return $this->handleSideEffect(
            DeleteApplicationLinks::create(['operatingCentre' => $aoc->getOperatingCentre()])
        );
    }
}
