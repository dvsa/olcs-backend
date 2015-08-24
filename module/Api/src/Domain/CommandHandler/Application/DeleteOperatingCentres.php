<?php

/**
 * Delete Operating Centres
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application;

use Doctrine\Common\Collections\Criteria;
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

    protected $extraRepos = [
        'ApplicationOperatingCentre',
        'ConditionUndertaking',
        'TransportManagerApplication',
        'TransportManagerLicence',
    ];

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
        $result = new Result();

        // we only want to delete where application.status = Under consideration
        if (!$aoc->getApplication()->isUnderConsideration()) {
            return $result;
        }

        $oc = $aoc->getOperatingCentre();

        $criteria = Criteria::create();
        $criteria->where($criteria->expr()->eq('application', $aoc->getApplication()));

        $conditionUndertakings = $oc->getConditionUndertakings()->matching($criteria);
        if (!is_null($conditionUndertakings)) {
            $count = 0;
            foreach ($conditionUndertakings as $cu) {
                $this->getRepo('ConditionUndertaking')->delete($cu);
                $count++;
            }
            $result->addMessage(
                sprintf(
                    "%d Condition/Undertaking(s) removed for Operating Centre %d",
                    $count,
                    $oc->getId()
                )
            );
        }

        return $result;
    }

    private function deleteTransportManagerLinks($loc)
    {
        $result = new Result();
        $operatingCentre = $loc->getOperatingCentre();

        // @todo move to separate command
        foreach ($operatingCentre->getTransportManagerLicences() as $tmLicence) {
            $tmLicence->getOperatingCentres()->removeElement($operatingCentre);
            $this->getRepo('TransportManagerLicence')->save($tmLicence);
        }

        foreach ($operatingCentre->getTransportManagerApplications() as $tmApplication) {
            if ($tmApplication->getApplication()->isUnderConsideration()) {
                $tmApplication->getOperatingCentres()->removeElement($operatingCentre);
                $this->getRepo('TransportManagerApplication')->save($tmApplication);
            }
        }

        $result->addMessage('Delinked TransportManagerLicence and TransportManagerApplication records '
            . 'from Operating Centre ' . $operatingCentre->getId());

        return $result;
    }

    private function deleteFromOtherApplications($aoc)
    {
        $result = new Result();
        $operatingCentre = $aoc->getOperatingCentre();

        // @todo move to separate command
        $count = 0;
        if ($operatingCentre->getApplications()) {
            foreach ($operatingCentre->getApplications() as $aoc) {
                if ($aoc->getApplication()->isUnderConsideration()) {
                    $this->getRepo('ApplicationOperatingCentre')->delete($aoc);
                    $count++;
                }
            }
        }

        $result->addMessage('Delinked Operating Centre from ' . $count . ' other Application(s)');

        return $result;
    }
}
