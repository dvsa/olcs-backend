<?php

/**
 * LicenceStatusRule.php
 *
 * @author Joshua Curtis <josh.curtis@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Exception;
use Dvsa\Olcs\Api\Entity\Licence\LicenceStatusRule as Entity;

/**
 * Licence Status Rule
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
class LicenceStatusRule extends AbstractRepository
{
    protected $entity = Entity::class;
}
