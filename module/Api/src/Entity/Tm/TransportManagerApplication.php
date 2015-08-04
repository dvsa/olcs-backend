<?php

namespace Dvsa\Olcs\Api\Entity\Tm;

use Doctrine\ORM\Mapping as ORM;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;

/**
 * TransportManagerApplication Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="transport_manager_application",
 *    indexes={
 *        @ORM\Index(name="ix_transport_manager_application_transport_manager_id", columns={"transport_manager_id"}),
 *        @ORM\Index(name="ix_transport_manager_application_application_id", columns={"application_id"}),
 *        @ORM\Index(name="ix_transport_manager_application_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_transport_manager_application_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_transport_manager_application_tm_type", columns={"tm_type"}),
 *        @ORM\Index(name="ix_transport_manager_application_tm_application_status", columns={"tm_application_status"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_transport_manager_application_olbs_key", columns={"olbs_key"})
 *    }
 * )
 */
class TransportManagerApplication extends AbstractTransportManagerApplication
{
    const ACTION_ADD    = 'A';
    const ACTION_UPDATE = 'U';
    const ACTION_DELETE = 'D';

    const STATUS_INCOMPLETE = 'tmap_st_incomplete';
    const STATUS_AWAITING_SIGNATURE = 'tmap_st_awaiting_signature';
    const STATUS_TM_SIGNED = 'tmap_st_tm_signed';
    const STATUS_OPERATOR_SIGNED = 'tmap_st_operator_signed';
    const STATUS_POSTAL_APPLICATION = 'tmap_st_postal_application';
    const STATUS_RECEIVED = 'tmap_st_received';

    const ERROR_MON = 'err_mon';
    const ERROR_TUE = 'err_tue';
    const ERROR_WED = 'err_wed';
    const ERROR_THU = 'err_thu';
    const ERROR_FRI = 'err_fri';
    const ERROR_SAT = 'err_sat';
    const ERROR_SUN = 'err_sun';

    public function updateTransportManagerApplication(
        $application,
        $transportManager,
        $action,
        $tmApplicationStatus,
        $createdBy = null
    ) {
        $this->setApplication($application);
        $this->setTransportManager($transportManager);
        $this->setAction($action);
        $this->setTmApplicationStatus($tmApplicationStatus);
        $this->setCreatedBy($createdBy);
    }

    public function updateTransportManagerApplicationFull(
        $lastModifiedBy = null,
        $tmType = null,
        $isOwner = null,
        $hoursMon = null,
        $hoursTue = null,
        $hoursWed = null,
        $hoursThu = null,
        $hoursFri = null,
        $hoursSat = null,
        $hoursSun = null,
        $additionalInformation = null,
        $tmApplicationStatus = null
    ) {
        $this->validateTransportManagerApplication(
            $hoursMon,
            $hoursTue,
            $hoursWed,
            $hoursThu,
            $hoursFri,
            $hoursSat,
            $hoursSun
        );
        $this->setLastModifiedBy($lastModifiedBy);
        $this->setTmType($tmType);
        $this->setIsOwner($isOwner);
        $this->setHoursMon($hoursMon);
        $this->setHoursTue($hoursTue);
        $this->setHoursWed($hoursWed);
        $this->setHoursThu($hoursThu);
        $this->setHoursFri($hoursFri);
        $this->setHoursSat($hoursSat);
        $this->setHoursSun($hoursSun);
        $this->setAdditionalInformation($additionalInformation);
        $this->setTmApplicationStatus($tmApplicationStatus);
    }

    protected function validateTransportManagerApplication(
        $hoursMon,
        $hoursTue,
        $hoursWed,
        $hoursThu,
        $hoursFri,
        $hoursSat,
        $hoursSun
    ) {
        $errors = [];
        if (!is_null($hoursMon)) {
            if ((int) $hoursMon < 0 || (int) $hoursMon > 24) {
                $errors[] = ['hoursMon' => [self::ERROR_MON => 'Mon must be between 0 and 24, inclusively']];
            }
        }
        if (!is_null($hoursTue)) {
            if ((int) $hoursTue < 0 || (int) $hoursTue > 24) {
                $errors[] = ['hoursTue' => [self::ERROR_TUE => 'Tue must be between 0 and 24, inclusively']];
            }
        }
        if (!is_null($hoursWed)) {
            if ((int) $hoursWed < 0 || (int) $hoursWed > 24) {
                $errors[] = ['hoursWed' => [self::ERROR_WED => 'Wed must be between 0 and 24, inclusively']];
            }
        }
        if (!is_null($hoursThu)) {
            if ((int) $hoursThu < 0 || (int) $hoursThu > 24) {
                $errors[] = ['hoursThu' => [self::ERROR_THU => 'Thu must be between 0 and 24, inclusively']];
            }
        }
        if (!is_null($hoursFri)) {
            if ((int) $hoursFri < 0 || (int) $hoursFri > 24) {
                $errors[] = ['hoursFri' => [self::ERROR_FRI => 'Fri must be between 0 and 24, inclusively']];
            }
        }
        if (!is_null($hoursSat)) {
            if ((int) $hoursSat < 0 || (int) $hoursSat > 24) {
                $errors[] = ['hoursSat' => [self::ERROR_SAT => 'Sat must be between 0 and 24, inclusively']];
            }
        }
        if (!is_null($hoursSun)) {
            if ((int) $hoursSun < 0 || (int) $hoursSun > 24) {
                $errors[] = ['hoursSun' => [self::ERROR_SUN => 'Sun must be between 0 and 24, inclusively']];
            }
        }
        if ($errors) {
            throw new ValidationException($errors);
        }
    }
}
