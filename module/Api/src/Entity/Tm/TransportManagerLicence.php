<?php

namespace Dvsa\Olcs\Api\Entity\Tm;

use Doctrine\ORM\Mapping as ORM;
use Dvsa\Olcs\Api\Entity\Licence\Licence;

/**
 * TransportManagerLicence Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="transport_manager_licence",
 *    indexes={
 *        @ORM\Index(name="ix_transport_manager_licence_transport_manager_id", columns={"transport_manager_id"}),
 *        @ORM\Index(name="ix_transport_manager_licence_licence_id", columns={"licence_id"}),
 *        @ORM\Index(name="ix_transport_manager_licence_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_transport_manager_licence_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_transport_manager_licence_tm_type", columns={"tm_type"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_transport_manager_licence_olbs_key", columns={"olbs_key"})
 *    }
 * )
 */
class TransportManagerLicence extends AbstractTransportManagerLicence
{
    public function __construct(Licence $licence, TransportManager $transportManager)
    {
        parent::__construct();

        $this->setTransportManager($transportManager);
        $this->setLicence($licence);
    }

    public function updateTransportManagerLicence(
        $tmType = null,
        $hoursMon = null,
        $hoursTue = null,
        $hoursWed = null,
        $hoursThu = null,
        $hoursFri = null,
        $hoursSat = null,
        $hoursSun = null,
        $additionalInformation = null,
        $lastModifiedBy = null
    ) {
        $this->validateTransportManagerLicence(
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
        $this->setHoursMon($hoursMon);
        $this->setHoursTue($hoursTue);
        $this->setHoursWed($hoursWed);
        $this->setHoursThu($hoursThu);
        $this->setHoursFri($hoursFri);
        $this->setHoursSat($hoursSat);
        $this->setHoursSun($hoursSun);
        $this->setAdditionalInformation($additionalInformation);
    }

    protected function validateTransportManagerLicence(
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
                $errors[] = ['hoursThu' => [self::ERROR_WED => 'Thu must be between 0 and 24, inclusively']];
            }
        }
        if (!is_null($hoursFri)) {
            if ((int) $hoursFri < 0 || (int) $hoursFri > 24) {
                $errors[] = ['hoursFri' => [self::ERROR_WED => 'Fri must be between 0 and 24, inclusively']];
            }
        }
        if (!is_null($hoursSat)) {
            if ((int) $hoursSat < 0 || (int) $hoursSat > 24) {
                $errors[] = ['hoursSat' => [self::ERROR_WED => 'Sat must be between 0 and 24, inclusively']];
            }
        }
        if (!is_null($hoursSun)) {
            if ((int) $hoursSun < 0 || (int) $hoursSun > 24) {
                $errors[] = ['hoursSun' => [self::ERROR_WED => 'Sun must be between 0 and 24, inclusively']];
            }
        }
        if ($errors) {
            throw new ValidationException($errors);
        }
    }
}
