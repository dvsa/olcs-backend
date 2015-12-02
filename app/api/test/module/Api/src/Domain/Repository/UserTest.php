<?php

/**
 * UserTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Dvsa\Olcs\Api\Domain\Repository\User as Repo;
use Dvsa\Olcs\Api\Domain\RepositoryServiceManager;
use Dvsa\Olcs\Api\Entity\Bus\LocalAuthority as LocalAuthorityEntity;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails as ContactDetailsEntity;
use Dvsa\Olcs\Api\Entity\User\Role as RoleEntity;
use Dvsa\Olcs\Api\Entity\User\Team as TeamEntity;
use Dvsa\Olcs\Api\Entity\Tm\TransportManager as TransportManagerEntity;
use Mockery as m;

/**
 * UserTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class UserTest extends RepositoryTestCase
{
    private $roleRepo;

    public function setUp()
    {
        $this->setUpSut(Repo::class);

        $this->roleRepo = m::mock();

        $sm = m::mock(RepositoryServiceManager::class);
        $sm->shouldReceive('get')->once()->with('Role')->andReturn($this->roleRepo);

        $this->sut->initService($sm);
    }

    public function testBuildDefaultQuery()
    {
        $mockQb = m::mock('Doctrine\ORM\QueryBuilder');

        $this->em->shouldReceive('getRepository->createQueryBuilder')->with('u')->once()->andReturn($mockQb);

        $this->queryBuilder->shouldReceive('modifyQuery')->with($mockQb)->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('withRefdata')->with()->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('byId')->with(834)->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('contactDetails', 'cd')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('cd.person')->once()->andReturnSelf();

        $mockQb->shouldReceive('getQuery->getResult')->once()->andReturn(['RESULT']);

        $this->assertSame('RESULT', $this->sut->fetchById(834));
    }

    /**
     * Had to mock SUT as the fetchList method uses Paginator which has proving time consuming to mock
     */
    public function testApplyListFilters()
    {
        $sut = m::mock(Repo::class);

        $mockQb = m::mock(\Doctrine\ORM\QueryBuilder::class);
        $query = \Dvsa\Olcs\Api\Domain\Query\User\UserListSelfserve::create(
            [
                'localAuthority' => 11,
                'partnerContactDetails' => 22,
                'organisation' => 43,
            ]
        );

        $mockQb->shouldReceive('andWhere')->with('localAuthority')->once()->andReturnSelf();
        $mockQb->shouldReceive('expr->eq')->with('u.localAuthority', ':localAuthority')->once()
            ->andReturn('localAuthority');
        $mockQb->shouldReceive('setParameter')->with('localAuthority', 11)->once();

        $mockQb->shouldReceive('andWhere')->with('partnerContactDetails')->once()->andReturnSelf();
        $mockQb->shouldReceive('expr->eq')->with('u.partnerContactDetails', ':partnerContactDetails')->once()
            ->andReturn('partnerContactDetails');
        $mockQb->shouldReceive('setParameter')->with('partnerContactDetails', 22)->once();

        $mockQb->shouldReceive('join')
            ->with('u.organisationUsers', 'ou', \Doctrine\ORM\Query\Expr\Join::WITH, 'ou.organisation = :organisation')
            ->once();
        $mockQb->shouldReceive('setParameter')->with('organisation', 43)->once();

        $sut->applyListFilters($mockQb, $query);
    }

    /**
     * Had to mock SUT as the fetchList method uses Paginator which has proving time consuming to mock
     */
    public function testApplyListFiltersUserList()
    {
        $sut = m::mock(Repo::class);

        $mockQb = m::mock(\Doctrine\ORM\QueryBuilder::class);
        $query = \Dvsa\Olcs\Transfer\Query\User\UserList::create(
            [
                'organisation' => 43,
                'team' => 112,
            ]
        );

        $mockQb->shouldReceive('join')
            ->with('u.organisationUsers', 'ou', \Doctrine\ORM\Query\Expr\Join::WITH, 'ou.organisation = :organisation')
            ->once();
        $mockQb->shouldReceive('setParameter')->with('organisation', 43)->once();

        $mockQb->shouldReceive('andWhere')->with('team')->once()->andReturnSelf();
        $mockQb->shouldReceive('expr->eq')->with('u.team', ':team')->once()
            ->andReturn('team');
        $mockQb->shouldReceive('setParameter')->with('team', 112)->once();

        $sut->applyListFilters($mockQb, $query);
    }

    /**
     * Had to mock SUT as the fetchList method uses Paginator which has proving time consuming to mock
     */
    public function testBuildDefaultListQuery()
    {
        $sut = m::mock(Repo::class)->makePartial()->shouldAllowMockingProtectedMethods();

        $mockQb = m::mock(\Doctrine\ORM\QueryBuilder::class);
        $mockQi = m::mock(\Dvsa\Olcs\Transfer\Query\QueryInterface::class);

        $sut->shouldReceive('getQueryBuilder')->with()->andReturn($mockQb);

        $mockQb->shouldReceive('modifyQuery')->with($mockQb)->once()->andReturnSelf();
        $mockQb->shouldReceive('withRefdata')->with()->once()->andReturnSelf();
        $mockQb->shouldReceive('with')->with('contactDetails', 'cd')->once()->andReturnSelf();
        $mockQb->shouldReceive('with')->with('cd.person')->once()->andReturnSelf();

        $sut->buildDefaultListQuery($mockQb, $mockQi);
    }

    public function testFetchForTma()
    {
        $mockQb = m::mock('Doctrine\ORM\QueryBuilder');

        $this->em->shouldReceive('getRepository->createQueryBuilder')->with('u')->once()->andReturn($mockQb);

        $this->queryBuilder->shouldReceive('modifyQuery')->with($mockQb)->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('withRefdata')->with()->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('u.contactDetails', 'cd')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('cd.person', 'cdp')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('with')->with('u.transportManager', 'tm')->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('byId')->with(1)->once()->andReturnSelf();

        $mockQb->shouldReceive('getQuery->getSingleResult')->once()->andReturn('RESULT');

        $this->assertSame('RESULT', $this->sut->fetchForTma(1));
    }

    public function testFetchByPid()
    {
        $mockQb = m::mock('Doctrine\ORM\QueryBuilder');

        $this->em->shouldReceive('getRepository->createQueryBuilder')->with('u')->once()->andReturn($mockQb);

        $this->queryBuilder->shouldReceive('modifyQuery')->with($mockQb)->once()->andReturnSelf();
        $this->queryBuilder->shouldReceive('withRefdata')->with()->once()->andReturnSelf();

        $mockQb->shouldReceive('where')->with('u.pid = :pid')->andReturnSelf();
        $mockQb->shouldReceive('setParameter')->with('pid', '123456');
        $mockQb->shouldReceive('getQuery->getSingleResult')->once()->andReturn('RESULT');

        $this->assertSame('RESULT', $this->sut->fetchByPid('123456'));
    }


    public function testPopulateRefDataReference()
    {
        $teamId = 1;
        $transportManagerId = 2;
        $partnerContactDetailsId = 3;
        $localAuthorityId = 4;
        $role = 'foo-role';

        $data = [
            'team' => $teamId,
            'transportManager' => $transportManagerId,
            'partnerContactDetails' => $partnerContactDetailsId,
            'localAuthority' => $localAuthorityId,
            'roles' => [$role]
        ];

        $teamEntity = m::mock(TeamEntity::class);
        $this->em->shouldReceive('getReference')
            ->once()
            ->with(TeamEntity::class, $teamId)
            ->andReturn($teamEntity);

        $transportManagerEntity = m::mock(TransportManagerEntity::class);
        $this->em->shouldReceive('getReference')
            ->once()
            ->with(TransportManagerEntity::class, $transportManagerId)
            ->andReturn($transportManagerEntity);

        $partnerContactDetailsEntity = m::mock(ContactDetailsEntity::class);
        $this->em->shouldReceive('getReference')
            ->once()
            ->with(ContactDetailsEntity::class, $partnerContactDetailsId)
            ->andReturn($partnerContactDetailsEntity);

        $localAuthorityEntity = m::mock(LocalAuthorityEntity::class);
        $this->em->shouldReceive('getReference')
            ->once()
            ->with(LocalAuthorityEntity::class, $localAuthorityId)
            ->andReturn($localAuthorityEntity);

        $roleEntity = m::mock(RoleEntity::class);
        $this->roleRepo->shouldReceive('fetchOneByRole')
            ->once()
            ->with($role)
            ->andReturn($roleEntity);

        $result = $this->sut->populateRefDataReference($data);

        $this->assertEquals(
            [
                'team' => $teamEntity,
                'transportManager' => $transportManagerEntity,
                'partnerContactDetails' => $partnerContactDetailsEntity,
                'localAuthority' => $localAuthorityEntity,
                'roles' => [$roleEntity],
            ],
            $result
        );
    }

    public function testFetchForRemindUsername()
    {
        $qb = $this->createMockQb('[QUERY]');

        $this->mockCreateQueryBuilder($qb);

        $qb->shouldReceive('getQuery')->andReturn(
            m::mock()->shouldReceive('execute')
                ->shouldReceive('getResult')
                ->andReturn(['RESULTS'])
                ->getMock()
        );
        $this->assertEquals(['RESULTS'], $this->sut->fetchForRemindUsername('ABC123', 'test@test.me'));

        $expectedQuery = '[QUERY] '
            . 'INNER JOIN u.contactDetails cd AND cd.emailAddress = [[test@test.me]] '
            . 'INNER JOIN u.organisationUsers ou '
            . 'INNER JOIN ou.organisation o '
            . 'INNER JOIN o.licences l AND l.licNo = [[ABC123]]';
        $this->assertEquals($expectedQuery, $this->query);
    }

    public function testFetchUsersCountByTeam()
    {
        $mockQb = m::mock('Doctrine\ORM\QueryBuilder');
        $this->em->shouldReceive('getRepository->createQueryBuilder')->with('u')->once()->andReturn($mockQb);

        $mockQb->shouldReceive('select')->with('count(u.id)')->once()->andReturnSelf();
        $mockQb->shouldReceive('expr->eq')->with('u.team', ':team')->once()->andReturn('expr');
        $mockQb->shouldReceive('andWhere')->with('expr')->once()->andReturnSelf();
        $mockQb->shouldReceive('setParameter')->with('team', 1)->once()->andReturnSelf();
        $mockQb->shouldReceive('getQuery->getSingleScalarResult')->once()->andReturn('result');

        $this->assertSame('result', $this->sut->fetchUsersCountByTeam(1));
    }
}
