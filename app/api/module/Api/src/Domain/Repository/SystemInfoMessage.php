<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Dvsa\Olcs\Api\Entity\System\SystemInfoMessage as SystemInfoMessageEntity;

/**
 * System Info Message Repository
 *
 * @author Dmitry Golubev <dmitrij.golubev@valtech.co.uk>
 */
class SystemInfoMessage extends AbstractRepository
{
    protected $entity = SystemInfoMessageEntity::class;
}
