<?php

/**
 * Constrained Countries
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\EcmtCountryConstraintLink as Entity;

/**
 * Constrained Countries
 */
class ConstrainedCountries extends AbstractRepository
{
    protected $entity = Entity::class;

}
