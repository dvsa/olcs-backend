<?php

/**
 * SubmissionSectionComment
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Entity\Submission\SubmissionSectionComment as Entity;

/**
 * SubmissionSectionComment
 */
class SubmissionSectionComment extends AbstractRepository
{
    protected $entity = Entity::class;
}
