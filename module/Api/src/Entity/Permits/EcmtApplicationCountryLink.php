<?php

namespace Dvsa\Olcs\Api\Entity\Permits;

use Doctrine\ORM\Mapping as ORM;

/**
 * EcmtApplicationRestrictedCountries Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="ecmt_application_country_link",
 *    indexes={
 *        @ORM\Index(name="ix_ecmt_application_country_link_ecmt_application_id",
     *     columns={"ecmt_application_id"}),
 *        @ORM\Index(name="ix_ecmt_application_country_link_country_id", columns={"country_id"})
 *    }
 * )
 */
class EcmtApplicationCountryLink extends AbstractEcmtApplicationCountryLink
{

    /**
     * Create new EcmtApplicationCountryLink
     *
     * @param RefData               $applicationRef           Ecmt Country Application
     * @param RefData               $countryRef               Country
     *
     * @return BusReg
     */
    public static function createNew(
        RefData $applicationRef,
        RefData $countryRef
      ) {
          /** @var EcmtApplicationRestrictedCountries $ecmtRestrictedCountries */
          $ecmtRestrictedCountries = new self();
          $ecmtRestrictedCountries->setEcmtApplication($applicationRef);
          $ecmtRestrictedCountries->setCountry($countryRef);

          return $ecmtRestrictedCountries;
      }
}
