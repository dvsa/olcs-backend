<?php

namespace Dvsa\Olcs\Api\Entity\Tm;

use Doctrine\ORM\Mapping as ORM;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use \Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Entity\OrganisationProviderInterface;

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
class TransportManagerLicence extends AbstractTransportManagerLicence implements OrganisationProviderInterface
{
    const ERROR_MON = 'err_mon';
    const ERROR_TUE = 'err_tue';
    const ERROR_WED = 'err_wed';
    const ERROR_THU = 'err_thu';
    const ERROR_FRI = 'err_fri';
    const ERROR_SAT = 'err_sat';
    const ERROR_SUN = 'err_sun';

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
        $isOwner = null
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
        $this->setTmType($tmType);
        $this->setHoursMon($hoursMon);
        $this->setHoursTue($hoursTue);
        $this->setHoursWed($hoursWed);
        $this->setHoursThu($hoursThu);
        $this->setHoursFri($hoursFri);
        $this->setHoursSat($hoursSat);
        $this->setHoursSun($hoursSun);
        $this->setAdditionalInformation($additionalInformation);
        $this->setIsOwner($isOwner);
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
            if ((float) $hoursMon < 0 || (float) $hoursMon > 24) {
                $errors[] = ['hoursMon' => [self::ERROR_MON => 'Mon must be between 0 and 24, inclusively']];
            }
        }
        if (!is_null($hoursTue)) {
            if ((float) $hoursTue < 0 || (float) $hoursTue > 24) {
                $errors[] = ['hoursTue' => [self::ERROR_TUE => 'Tue must be between 0 and 24, inclusively']];
            }
        }
        if (!is_null($hoursWed)) {
            if ((float) $hoursWed < 0 || (float) $hoursWed > 24) {
                $errors[] = ['hoursWed' => [self::ERROR_WED => 'Wed must be between 0 and 24, inclusively']];
            }
        }
        if (!is_null($hoursThu)) {
            if ((float) $hoursThu < 0 || (float) $hoursThu > 24) {
                $errors[] = ['hoursThu' => [self::ERROR_WED => 'Thu must be between 0 and 24, inclusively']];
            }
        }
        if (!is_null($hoursFri)) {
            if ((float) $hoursFri < 0 || (float) $hoursFri > 24) {
                $errors[] = ['hoursFri' => [self::ERROR_WED => 'Fri must be between 0 and 24, inclusively']];
            }
        }
        if (!is_null($hoursSat)) {
            if ((float) $hoursSat < 0 || (float) $hoursSat > 24) {
                $errors[] = ['hoursSat' => [self::ERROR_WED => 'Sat must be between 0 and 24, inclusively']];
            }
        }
        if (!is_null($hoursSun)) {
            if ((float) $hoursSun < 0 || (float) $hoursSun > 24) {
                $errors[] = ['hoursSun' => [self::ERROR_WED => 'Sun must be between 0 and 24, inclusively']];
            }
        }
        if ($errors) {
            throw new ValidationException($errors);
        }
    }

    /**
     * Simple method to sum all the daily hours to give a weekly total
     * @return int
     */
    public function getTotalWeeklyHours()
    {
        $weeklyHours = 0;

        $weeklyHours += (int) $this->getHoursMon();
        $weeklyHours += (int) $this->getHoursTue();
        $weeklyHours += (int) $this->getHoursWed();
        $weeklyHours += (int) $this->getHoursThu();
        $weeklyHours += (int) $this->getHoursFri();
        $weeklyHours += (int) $this->getHoursSat();
        $weeklyHours += (int) $this->getHoursSun();

        return $weeklyHours;
    }

    /**
     * @inheritdoc
     */
    public function getRelatedOrganisation()
    {
        return $this->getLicence()->getOrganisation();
    }
}
