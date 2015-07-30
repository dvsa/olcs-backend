<?php

/**
 * Update Operating Centre Helper
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Service;

/**
 * Update Operating Centre Helper
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class UpdateOperatingCentreHelper
{
    protected $messages = [];

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

    public function getMessages()
    {
        return $this->messages;
    }

    public function validateTotalAuthVehicles($entity, $command, $totals)
    {
        if ($entity->isRestricted() && $command->getTotAuthVehicles() > 2) {
            $this->addMessage('totAuthVehicles', self::ERR_OC_R_1);
        }

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

    public function validateTotalAuthTrailers($command, $totals)
    {
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
    }

    public function validatePsv($entity, $command)
    {
        $sum = (int)$command->getTotAuthSmallVehicles()
            + (int)$command->getTotAuthMediumVehicles()
            + (int)$command->getTotAuthLargeVehicles();

        if ($sum != $command->getTotAuthVehicles()) {
            if ($entity->canHaveLargeVehicles()) {
                $message = self::ERR_OC_PSV_SUM_1A;
            } else {
                $message = self::ERR_OC_PSV_SUM_1B;
            }
            $this->addMessage('totAuthVehicles', $message);
        }
    }

    public function addMessage($field, $messageCode, $message = null)
    {
        if ($message === null) {
            $message = $messageCode;
        }

        $this->messages[$field][] = [$messageCode => $message];
    }
}
