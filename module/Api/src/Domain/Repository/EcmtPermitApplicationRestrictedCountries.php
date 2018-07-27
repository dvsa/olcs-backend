<?php

/**
 * Restricted Countries
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Permits\EcmtApplicationRestrictedCountries as Entity;

/**
 * Restricted Countries
 */
class EcmtApplicationRestrictedCountries extends AbstractRepository
{
    protected $countries = Entity::class;
}
