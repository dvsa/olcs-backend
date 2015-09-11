<?php

/**
 * UserTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Dvsa\Olcs\Api\Domain\Repository\User as Repo;
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
    public function setUp()
    {
        $this->setUpSut(Repo::class);
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
        $mockQi = m::mock(\Dvsa\Olcs\Transfer\Query\QueryInterface::class);

        $mockQi->shouldReceive('getOrganisation')->with()->twice()->andReturn(43);

        $mockQb->shouldReceive('join')
            ->with('u.organisationUsers', 'ou', \Doctrine\ORM\Query\Expr\Join::WITH, 'ou.organisation = :organisation')
            ->once();
        $mockQb->shouldReceive('setParameter')->with('organisation', 43)->once();

        $sut->applyListFilters($mockQb, $mockQi);
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

    public function testPopulateRefDataReference()
    {
        $teamId = 1;
        $transportManagerId = 2;
        $partnerContactDetailsId = 3;
        $localAuthorityId = 4;
        $roleId = 100;

        $data = [
            'team' => $teamId,
            'transportManager' => $transportManagerId,
            'partnerContactDetails' => $partnerContactDetailsId,
            'localAuthority' => $localAuthorityId,
            'roles' => [$roleId]
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
        $this->em->shouldReceive('getReference')
            ->once()
            ->with(RoleEntity::class, $roleId)
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
}
