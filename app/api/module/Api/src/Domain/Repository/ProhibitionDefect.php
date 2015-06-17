<?php
/**
 * ProhibitionDefect Repo
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Prohibition\ProhibitionDefect as ProhibitionDefectEntity;

/**
 * ProhibitionDefect Repo
 */
class ProhibitionDefect extends AbstractRepository
{
    /**
     * @var ProhibitionDefectEntity
     */
    protected $entity = ProhibitionDefectEntity::class;
}
