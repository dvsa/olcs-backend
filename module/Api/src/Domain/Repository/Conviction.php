<?php
/**
 * Conviction Repo
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Cases\Conviction as ConvictionEntity;

/**
 * Conviction Repo
 */
class Conviction extends AbstractRepository
{
    /**
     * @var ConvictionEntity
     */
    protected $entity = ConvictionEntity::class;
}
