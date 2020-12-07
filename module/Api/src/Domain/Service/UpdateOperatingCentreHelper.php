<?php

/**
 * Update Operating Centre Helper
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Service;

use Dvsa\Olcs\Api\Entity\User\Permission;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use ZfcRbac\Service\AuthorizationService;

/**
 * Update Operating Centre Helper
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class UpdateOperatingCentreHelper implements FactoryInterface
{
    protected $messages = [];

    const ERR_OC_R_1 = 'ERR_OC_R_1'; // restricted-too-many
    const ERR_OC_V_1 = 'ERR_OC_V_1'; // 1-operating-centre
    const ERR_OC_V_2 = 'ERR_OC_V_2'; // too-low
    const ERR_OC_V_3 = 'ERR_OC_V_3'; // too-high
    const ERR_OC_V_4 = 'ERR_OC_V_4'; // no-operating-centre
    const ERR_OC_T_1 = 'ERR_OC_T_1'; // 1-operating-centre
    const ERR_OC_T_2 = 'ERR_OC_T_2'; // too-low
    const ERR_OC_T_3 = 'ERR_OC_T_3'; // too-high
    const ERR_OC_T_4 = 'ERR_OC_T_4'; // no-operating-centre
    const ERR_OC_EA_EMPTY = 'ERR_OC_EA_EMPTY';

    /**
     * @var AuthorizationService
     */
    protected $authService;

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->authService = $serviceLocator->get(AuthorizationService::class);

        return $this;
    }

    public function getMessages()
    {
        return $this->messages;
    }

    public function validateEnforcementArea($entity, $command)
    {
        $ea = $command->getEnforcementArea();

        if ($this->authService->isGranted(Permission::INTERNAL_USER)
            && $entity->getTrafficArea() !== null
            && empty($ea)
        ) {
            $this->addMessage('enforcementArea', self::ERR_OC_EA_EMPTY);
        }
    }

    public function validateTotalAuthVehicles($entity, $command, $totals)
    {
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
        if ($entity->isRestricted() && $command->getTotAuthVehicles() > 2) {
            $this->addMessage('totAuthVehicles', self::ERR_OC_R_1);
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
