<?php

/**
 * User
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Bus\LocalAuthority as LocalAuthorityEntity;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails as ContactDetailsEntity;
use Dvsa\Olcs\Api\Entity\User\User as Entity;
use Dvsa\Olcs\Api\Entity\User\Team as TeamEntity;
use Dvsa\Olcs\Api\Entity\Tm\TransportManager as TransportManagerEntity;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Doctrine\ORM\Query\Expr;

/**
 * User
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class User extends AbstractRepository
{
    protected $entity = Entity::class;
    protected $alias = 'u';

    /**
     * @param QueryBuilder $qb
     * @param int          $id
     */
    protected function buildDefaultQuery(QueryBuilder $qb, $id)
    {
        parent::buildDefaultQuery($qb, $id);

        // join in person details
        $this->getQueryBuilder()->with('contactDetails', 'cd')->with('cd.person');
    }

    /**
     * @param QueryBuilder   $qb
     * @param QueryInterface $query
     */
    protected function buildDefaultListQuery(QueryBuilder $qb, QueryInterface $query)
    {
        parent::buildDefaultListQuery($qb, $query);

        // join in person details
        $this->getQueryBuilder()->with('contactDetails', 'cd')->with('cd.person');
    }

    /**
     * @param QueryBuilder   $qb
     * @param QueryInterface $query
     */
    protected function applyListFilters(QueryBuilder $qb, QueryInterface $query)
    {
        // filter by organisation if it has been specified
        if ($query->getOrganisation()) {
            $qb->join('u.organisationUsers', 'ou', Expr\Join::WITH, 'ou.organisation = :organisation');
            $qb->setParameter('organisation', $query->getOrganisation());
        }
    }

    public function fetchForTma($userId)
    {
        $qb = $this->createQueryBuilder();

        $this->getQueryBuilder()->modifyQuery($qb)
            ->withRefdata()
            ->with($this->alias .'.contactDetails', 'cd')
            ->with('cd.person', 'cdp')
            ->with($this->alias .'.transportManager', 'tm')
            ->byId($userId);

        return $qb->getQuery()->getSingleResult();
    }

    /**
     * @param array $data Array of data as defined by Dvsa\Olcs\Transfer\Command\User\CreateUser
     * @return array
     */
    public function populateRefDataReference(array $data)
    {
        if (isset($data['team'])) {
            $data['team'] = $this->getReference(TeamEntity::class, $data['team']);
        }

        if (isset($data['transportManager'])) {
            $data['transportManager'] = $this->getReference(TransportManagerEntity::class, $data['transportManager']);
        }

        if (isset($data['partnerContactDetails'])) {
            $data['partnerContactDetails'] = $this->getReference(
                ContactDetailsEntity::class,
                $data['partnerContactDetails']
            );
        }

        if (isset($data['localAuthority'])) {
            $data['localAuthority'] = $this->getReference(LocalAuthorityEntity::class, $data['localAuthority']);
        }

        return $data;
    }
}
