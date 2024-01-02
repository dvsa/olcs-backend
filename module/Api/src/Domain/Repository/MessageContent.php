<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Entity\Messaging\MessagingMessage;
use Dvsa\Olcs\Api\Entity\Messaging\MessagingContent as Entity;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

class MessageContent extends AbstractRepository
{
    protected $entity = Entity::class;
}
