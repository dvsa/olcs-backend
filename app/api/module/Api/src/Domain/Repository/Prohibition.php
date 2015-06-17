<?php
/**
 * Prohibition Entity
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Prohibition\Prohibition as ProhibitionEntity;

/**
 * Prohibition Entity
 */
class Prohibition extends AbstractRepository
{
    /**
     * @var ProhibitionEntity
     */
    protected $entity = ProhibitionEntity::class;
}
