<?php

/**
 * Update Operating Centres
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Licence;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Entity\Licence\LicenceOperatingCentre;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Transfer\Command\Licence\UpdateOperatingCentres as Cmd;

/**
 * Update Operating Centres
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class UpdateOperatingCentres extends AbstractCommandHandler implements TransactionedInterface
{
    const ERR_OC_PSV_SUM_1A = 'ERR_OC_PSV_SUM_1A'; // with large
    const ERR_OC_PSV_SUM_1B = 'ERR_OC_PSV_SUM_1B'; // without large
    const ERR_OC_R_1 = 'ERR_OC_R_1'; // restricted-too-many
    const ERR_OC_V_1 = 'ERR_OC_V_1'; // 1-operating-centre
    const ERR_OC_V_2 = 'ERR_OC_V_2'; // too-low
    const ERR_OC_V_3 = 'ERR_OC_V_3'; // too-high
    const ERR_OC_V_4 = 'ERR_OC_V_4'; // no-operating-centre
    const ERR_OC_T_1 = 'ERR_OC_T_1'; // 1-operating-centre
    const ERR_OC_T_2 = 'ERR_OC_T_2'; // too-low
    const ERR_OC_T_3 = 'ERR_OC_T_3'; // too-high
    const ERR_OC_T_4 = 'ERR_OC_T_4'; // no-operating-centre

    protected $repoServiceName = 'Licence';

    protected $extraRepos = ['LicenceOperatingCentre'];

    private $totals;

    private $messages = [];

    /**
     * @param Cmd $command
     */
    public function handleCommand(CommandInterface $command)
    {
        /** @var Licence $licence */
        $licence = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT, $command->getVersion());

        $this->validate($licence, $command);

        if ($licence->isPsv()) {
            $licence->setTotAuthSmallVehicles($command->getTotAuthSmallVehicles());
            $licence->setTotAuthMediumVehicles($command->getTotAuthMediumVehicles());

            if ($licence->canHaveLargeVehicles()) {
                $licence->setTotAuthLargeVehicles($command->getTotAuthLargeVehicles());
            }
        } else {
            $licence->setTotAuthTrailers($command->getTotAuthTrailers());
        }

        $licence->setTotAuthVehicles($command->getTotAuthVehicles());

        $this->getRepo()->save($licence);
        $this->result->addMessage('Licence record updated');

        return $this->result;
    }

    protected function validate(Licence $licence, Cmd $command)
    {
        if (!$command->getPartial()) {
            if ($licence->isPsv()) {
                $this->validatePsv($licence, $command);
            } else {
                $this->validateTotalAuthTrailers($licence, $command);
            }

            $this->validateTotalAuthVehicles($licence, $command);
        }

        if (!empty($this->messages)) {
            throw new ValidationException($this->messages);
        }
    }

    protected function validateTotalAuthVehicles(Licence $licence, Cmd $command)
    {
        if ($licence->isRestricted() && $command->getTotAuthVehicles() > 2) {
            $this->addMessage('totAuthVehicles', self::ERR_OC_R_1);
        }

        $totals = $this->getTotals($licence);

        if ($totals['noOfOperatingCentres'] === 0) {
            $this->addMessage('totAuthVehicles', self::ERR_OC_V_4);
        }

        if ($totals['noOfOperatingCentres'] === 1 && $command->getTotAuthVehicles() != $totals['minVehicleAuth']) {
            $this->addMessage('totAuthVehicles', self::ERR_OC_V_1);
        }

        if ($totals['noOfOperatingCentres'] >= 2) {

            if ($command->getTotAuthVehicles() < $totals['minVehicleAuth']) {
                $this->addMessage('totAuthVehicles', self::ERR_OC_V_2);
            }

            if ($command->getTotAuthVehicles() > $totals['maxVehicleAuth']) {
                $this->addMessage('totAuthVehicles', self::ERR_OC_V_3);
            }
        }
    }

    protected function validateTotalAuthTrailers(Licence $licence, Cmd $command)
    {
        $totals = $this->getTotals($licence);

        if ($totals['noOfOperatingCentres'] === 0) {
            $this->addMessage('totAuthTrailers', self::ERR_OC_T_4);
        }

        if ($totals['noOfOperatingCentres'] === 1 && $command->getTotAuthTrailers() != $totals['minTrailerAuth']) {
            $this->addMessage('totAuthTrailers', self::ERR_OC_T_1);
        }

        if ($totals['noOfOperatingCentres'] >= 2) {

            if ($command->getTotAuthTrailers() < $totals['minTrailerAuth']) {
                $this->addMessage('totAuthTrailers', self::ERR_OC_T_2);
            }

            if ($command->getTotAuthTrailers() > $totals['maxTrailerAuth']) {
                $this->addMessage('totAuthTrailers', self::ERR_OC_T_3);
            }
        }

        return [];
    }

    protected function validatePsv(Licence $licence, Cmd $command)
    {
        $sum = (int)$command->getTotAuthSmallVehicles()
            + (int)$command->getTotAuthMediumVehicles()
            + (int)$command->getTotAuthLargeVehicles();

        if ($sum != $command->getTotAuthVehicles()) {
            if ($licence->canHaveLargeVehicles()) {
                $message = self::ERR_OC_PSV_SUM_1A;
            } else {
                $message = self::ERR_OC_PSV_SUM_1B;
            }
            $this->addMessage('totAuthVehicles', $message);
        }
    }

    protected function getTotals(Licence $licence)
    {
        if ($this->totals !== null) {
            return $this->totals;
        }

        $locs = $licence->getOperatingCentres();

        $this->totals['noOfOperatingCentres'] = 0;
        $this->totals['minVehicleAuth'] = 0;
        $this->totals['maxVehicleAuth'] = 0;
        $this->totals['minTrailerAuth'] = 0;
        $this->totals['maxTrailerAuth'] = 0;

        /** @var LicenceOperatingCentre $loc */
        foreach ($locs as $loc) {

            $this->totals['noOfOperatingCentres']++;

            $this->totals['minVehicleAuth'] = max([$this->totals['minVehicleAuth'], $loc->getNoOfVehiclesRequired()]);
            $this->totals['minTrailerAuth'] = max([$this->totals['minTrailerAuth'], $loc->getNoOfTrailersRequired()]);

            $this->totals['maxVehicleAuth'] += (int)$loc->getNoOfVehiclesRequired();
            $this->totals['maxTrailerAuth'] += (int)$loc->getNoOfTrailersRequired();
        }

        return $this->totals;
    }

    protected function addMessage($field, $messageCode, $message = null)
    {
        if ($message === null) {
            $message = $messageCode;
        }

        $this->messages[$field][] = [$messageCode => $message];
    }
}
