<?php

namespace Dvsa\Olcs\Api\Entity\Permits;

use Doctrine\ORM\Mapping as ORM;

/**
 * EcmtApplicationRestrictedCountries Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="ecmt_application_restricted_countries",
 *    indexes={
 *        @ORM\Index(name="ix_ecmt_application_country_link_ecmt_application_id",
     *     columns={"ecmt_application_id"}),
 *        @ORM\Index(name="ix_ecmt_application_country_link_country_id", columns={"country_id"})
 *    }
 * )
 */
class EcmtApplicationRestrictedCountries extends AbstractEcmtApplicationRestrictedCountries
{
    public static function createNew(
        Application $application,
        $countryId
      ) {
          $ecmtRestrictedCountries = new self();
          $ecmtRestrictedCountries->setApplicationId($application);
          $ecmtRestrictedCountries->setCountryId($countryId);

          return $ecmtRestrictedCountries;
      }
}
