<?php

/**
 * With Person Contact details. Returns Contact details + address, person and phone contact information
 */

namespace Dvsa\Olcs\Api\Domain\QueryPartial;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

/**
 * With PersonContactDetails
 */
final class WithPersonContactDetails implements QueryPartialInterface
{
    /**
     * @var With
     */
    private $with;

    public function __construct(QueryPartialInterface $with)
    {
        $this->with = $with;
    }

    /**
     * Joins on all contact details / address / person / phone contact relationships
     *
     * @param QueryBuilder $qb
     * @param array $arguments
     */
    public function modifyQuery(QueryBuilder $qb, array $arguments = [])
    {
        $column = (isset($arguments[0])) ? $arguments[0] : 'contactDetails';

        if (strpos($column, '.') === false) {
            $column = $qb->getRootAliases()[0] . '.' . $column;
        }

        $this->with->modifyQuery($qb, [$column, 'c']);
        $this->with->modifyQuery($qb, ['c.person', 'p']);
        $this->with->modifyQuery($qb, ['c.address', 'a']);
        $this->with->modifyQuery($qb, ['c.contactType', 'ct']);
        $this->with->modifyQuery($qb, ['c.phoneContacts', 'pc']);
    }
}
