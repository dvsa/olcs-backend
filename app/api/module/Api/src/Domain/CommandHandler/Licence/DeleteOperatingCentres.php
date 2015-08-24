<?php

/**
 * Delete Operating Centres
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Licence;

use Doctrine\Common\Collections\Criteria;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Licence\LicenceOperatingCentre;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\Licence\DeleteOperatingCentres as Cmd;

/**
 * Delete Operating Centres
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class DeleteOperatingCentres extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Licence';

    protected $extraRepos = [
        'LicenceOperatingCentre',
        'ConditionUndertaking',
        'TransportManagerLicence',
        'TransportManagerApplication',
        'ApplicationOperatingCentre',
    ];

    /**
     * @param Cmd $command
     * @return \Dvsa\Olcs\Api\Domain\Command\Result
     */
    public function handleCommand(CommandInterface $command)
    {
        /** @var LicenceEntity $licence */
        $licence = $this->getRepo()->fetchById($command->getLicence());

        $locs = $licence->getOperatingCentres();

        $count = 0;

        /** @var LicenceOperatingCentre $loc */
        foreach ($locs as $loc) {
            if (in_array($loc->getId(), $command->getIds())) {
                $message = $loc->checkCanDelete();
                if ($message) {
                    throw new \Dvsa\Olcs\Api\Domain\Exception\BadRequestException(key($message));
                }

                $count++;
                $this->getRepo('LicenceOperatingCentre')->delete($loc);
                $this->result->merge($this->deleteConditionUndertakings($loc));
                $this->result->merge($this->deleteTransportManagerLinks($loc));
                $this->result->merge($this->deleteFromOtherApplications($loc));
            }
        }

        $this->result->addMessage($count . ' Operating Centre(s) removed');

        return $this->result;
    }

    /**
     * @param LicenceOperatingCentre $loc
     * @return Result
     */
    private function deleteConditionUndertakings($loc)
    {
        $result = new Result();

        $oc = $loc->getOperatingCentre();

        $criteria = Criteria::create();
        $criteria->where($criteria->expr()->eq('licence', $loc->getLicence()));

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

    private function deleteFromOtherApplications($loc)
    {
        $result = new Result();
        $operatingCentre = $loc->getOperatingCentre();

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
