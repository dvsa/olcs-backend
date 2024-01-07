<?php

namespace Dvsa\Olcs\Api\Domain\QueryPartial;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

/**
 * With Team
 */
final class WithCreatedByWithTeam implements QueryPartialInterface
{
    /**
     * @var With
     */
    private $with;

    public function __construct(With $with)
    {
        $this->with = $with;
    }

    /**
     * Joins on all refdata relationships
     *
     * @param QueryBuilder $qb
     * @param array $arguments
     */
    public function modifyQuery(QueryBuilder $qb, array $arguments = [])
    {
        $alias = ((isset($arguments[0]) && isset($arguments[1])) ? $arguments[1] : $qb->getRootAliases()[0]);

        $this->with->modifyQuery($qb, [$alias . '.createdBy', 'u']);
        $this->with->modifyQuery($qb, ['u.contactDetails', 'cd']);
        $this->with->modifyQuery($qb, ['cd.person', 'p']);
        $this->with->modifyQuery($qb, ['u.team', 'tm']);
    }
}
