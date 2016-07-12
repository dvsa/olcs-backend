<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Domain\RepositoryServiceManager;
use Dvsa\Olcs\Api\Entity\Bus\LocalAuthority as LocalAuthorityEntity;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails as ContactDetailsEntity;
use Dvsa\Olcs\Api\Entity\User\User as Entity;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Entity\User\Team as TeamEntity;
use Dvsa\Olcs\Api\Entity\Tm\TransportManager as TransportManagerEntity;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Doctrine\ORM\Query\Expr;
use Dvsa\Olcs\Api\Rbac\PidIdentityProvider as PidIdentityProviderEntity;

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
     * @var Role
     */
    protected $roleRepo;

    /**
     * Called from the factory to allow additional services to be injected
     *
     * @param RepositoryServiceManager $serviceManager Repository manager
     *
     * @return void
     */
    public function initService(RepositoryServiceManager $serviceManager)
    {
        $this->roleRepo = $serviceManager->get('Role');
    }

    /**
     * Build default query
     *
     * @param QueryBuilder $qb Query
     * @param int          $id Id
     *
     * @return void
     */
    protected function buildDefaultQuery(QueryBuilder $qb, $id)
    {
        parent::buildDefaultQuery($qb, $id);

        // join in person and team details
        $this->getQueryBuilder()
            ->with('team', 't')
            ->with('contactDetails', 'cd')
            ->with('cd.person', 'p');
    }

    /**
     * Build default list query
     *
     * @param QueryBuilder   $qb              Query Builder
     * @param QueryInterface $query           Query
     * @param array          $compositeFields fields
     *
     * @return void
     */
    protected function buildDefaultListQuery(QueryBuilder $qb, QueryInterface $query, $compositeFields = [])
    {
        parent::buildDefaultListQuery($qb, $query, $compositeFields);

        // join in person and team details
        $this->getQueryBuilder()
            ->with('team', 't')
            ->with('contactDetails', 'cd')
            ->with('cd.person', 'p')
            ->with('p.disqualifications', 'd');
    }

    /**
     * Apply filters
     *
     * @param QueryBuilder   $qb    Query Builder
     * @param QueryInterface $query Query
     *
     * @return void
     */
    protected function applyListFilters(QueryBuilder $qb, QueryInterface $query)
    {
        if (method_exists($query, 'getLocalAuthority') && !empty($query->getLocalAuthority())) {
            $qb->andWhere($qb->expr()->eq($this->alias . '.localAuthority', ':localAuthority'))
                ->setParameter('localAuthority', $query->getLocalAuthority());
        }

        if (method_exists($query, 'getPartnerContactDetails') && !empty($query->getPartnerContactDetails())) {
            $qb->andWhere($qb->expr()->eq($this->alias . '.partnerContactDetails', ':partnerContactDetails'))
                ->setParameter('partnerContactDetails', $query->getPartnerContactDetails());
        }

        // filter by organisation if it has been specified
        if (method_exists($query, 'getOrganisation') && !empty($query->getOrganisation())) {
            $qb->join('u.organisationUsers', 'ou', Expr\Join::WITH, 'ou.organisation = :organisation');
            $qb->setParameter('organisation', (int) $query->getOrganisation());
        }

        // filter by team if it has been specified
        if (method_exists($query, 'getTeam') && !empty($query->getTeam())) {
            $qb->andWhere($qb->expr()->eq($this->alias . '.team', ':team'))
                ->setParameter('team', (int) $query->getTeam());
        }

        if (method_exists($query, 'getIsInternal') && $query->getIsInternal() == true) {
            $qb->andWhere($qb->expr()->isNotNull($this->alias . '.team'));
        }

        // exclude system user from all lists
        $qb->andWhere($qb->expr()->neq($this->alias . '.id', ':systemUser'))
            ->setParameter('systemUser', PidIdentityProviderEntity::SYSTEM_USER);
    }

    /**
     * Fetch transport manager by user id
     *
     * @param int $userId User identifier
     *
     * @return mixed
     */
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
     * Get a list of users by licence number and email address
     *
     * @param string $licNo        Licence number
     * @param string $emailAddress email address
     *
     * @return array
     */
    public function fetchForRemindUsername($licNo, $emailAddress)
    {
        $qb = $this->createQueryBuilder();

        $qb
            // match by email address
            ->innerJoin($this->alias . '.contactDetails', 'cd')
            ->andWhere($qb->expr()->eq('cd.emailAddress', ':emailAddress'))
            ->setParameter('emailAddress', $emailAddress)

            // match by licence number
            ->innerJoin($this->alias . '.organisationUsers', 'ou')
            ->innerJoin('ou.organisation', 'o')
            ->innerJoin('o.licences', 'l')
            ->andWhere($qb->expr()->eq('l.licNo', ':licNo'))
            ->setParameter('licNo', $licNo);

        $query = $qb->getQuery();
        $query->execute();

        return $query->getResult();
    }

    /**
     * Populate Ref Data References
     *
     * @param array $data Array of data as defined by Dvsa\Olcs\Transfer\Command\User\CreateUser
     *
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

        if (isset($data['roles'])) {
            $data['roles'] = array_map(
                function ($role) {
                    return $this->roleRepo->fetchOneByRole($role);
                },
                $data['roles']
            );
        }

        return $data;
    }

    /**
     * Get count of users in team
     *
     * @param int $teamId Team indentifier
     *
     * @return int|null
     */
    public function fetchUsersCountByTeam($teamId)
    {
        $qb = $this->createQueryBuilder();
        $qb->select('count(' . $this->alias . '.id)')
            ->andWhere($qb->expr()->eq($this->alias . '.team', ':team'))
            ->setParameter('team', $teamId);

        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * Get by pid
     *
     * @param int $pid Pid
     *
     * @return Entity|null
     */
    public function fetchByPid($pid)
    {
        $qb = $this->createQueryBuilder();
        $this->getQueryBuilder()
            ->modifyQuery($qb)
            ->withRefdata()
            ->withPersonContactDetails()
            ->with('team')
            ->with('organisationUsers')
            ->with('roles')
            ->with('transportManager')
            ->with('localAuthority');

        $qb->where($this->alias . '.pid = :pid')->setParameter('pid', $pid);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
