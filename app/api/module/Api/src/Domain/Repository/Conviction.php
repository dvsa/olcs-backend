<?php
/**
 * Conviction Entity
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Entity\Cases\Conviction as ConvictionEntity;
use Dvsa\Olcs\Transfer\Query\Processing\NoteList as NoteDTO;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Conviction Entity
 */
class Conviction extends AbstractRepository
{
    /**
     * @var ConvictionEntity
     */
    protected $entity = ConvictionEntity::class;
}
