<?php

/**
 * UserListSelfserveTest
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\User;

use Dvsa\Olcs\Api\Domain\QueryHandler\User\UserListSelfserve as QueryHandler;
use Dvsa\Olcs\Api\Domain\Repository\User as Repo;
use Dvsa\Olcs\Api\Entity\Bus\LocalAuthority as LocalAuthorityEntity;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails as ContactDetailsEntity;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation as OrganisationEntity;
use Dvsa\Olcs\Api\Entity\Organisation\OrganisationUser as OrganisationUserEntity;
use Dvsa\Olcs\Api\Entity\User\User as UserEntity;
use Dvsa\Olcs\Transfer\Query\User\UserList as Query;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;
use ZfcRbac\Service\AuthorizationService;

/**
 * UserListSelfserveTest
 */
class UserListSelfserveTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new QueryHandler();
        $this->mockRepo('User', Repo::class);

        $this->mockedSmServices = [
            AuthorizationService::class => m::mock(AuthorizationService::class)
        ];

        parent::setUp();
    }

    public function commonHandleQueryTest()
    {
        $query = Query::create(['QUERY']);

        $user = m::mock(\Dvsa\Olcs\Api\Entity\User\User::class)->makePartial();
        $user->setId(74);

        $this->repoMap['User']->shouldReceive('fetchList')->andReturn([$user]);
        $this->repoMap['User']->shouldReceive('fetchCount')->andReturn('COUNT');

        $result = $this->sut->handleQuery($query);

        $this->assertSame(74, $result['result'][0]['id']);
        $this->assertSame('COUNT', $result['count']);
    }

    public function testHandleQueryForPartner()
    {
        /** @var ContactDetailsEntity $partnerContactDetails */
        $partnerContactDetails = m::mock(ContactDetailsEntity::class)->makePartial();
        $partnerContactDetails->setId(1000);
        $partnerContactDetails->shouldReceive('getId')->once()->andReturn(1000);

        /** @var UserEntity $currentUser */
        $currentUser = m::mock(UserEntity::class)->makePartial();
        $currentUser->setId(222);
        $currentUser->setPartnerContactDetails($partnerContactDetails);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('getIdentity->getUser')
            ->andReturn($currentUser);

        $this->commonHandleQueryTest();
    }

    public function testHandleQueryForLocalAuthority()
    {
        /** @var LocalAuthorityEntity $localAuthority */
        $localAuthority = m::mock(LocalAuthorityEntity::class)->makePartial();
        $localAuthority->setId(1000);
        $localAuthority->shouldReceive('getId')->once()->andReturn(1000);

        /** @var UserEntity $currentUser */
        $currentUser = m::mock(UserEntity::class)->makePartial();
        $currentUser->setId(222);
        $currentUser->setLocalAuthority($localAuthority);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('getIdentity->getUser')
            ->andReturn($currentUser);

        $this->commonHandleQueryTest();
    }

    public function testHandleQueryForOperator()
    {
        /** @var OrganisationEntity $organisation */
        $organisation = m::mock(OrganisationEntity::class)->makePartial();
        $organisation->setId(1000);
        $organisation->shouldReceive('getId')->once()->andReturn(1000);

        /** @var OrganisationUserEntity $organisation */
        $organisationUser = m::mock(OrganisationUserEntity::class)->makePartial();
        $organisationUser->setOrganisation($organisation);

        /** @var UserEntity $currentUser */
        $currentUser = new UserEntity(UserEntity::USER_TYPE_SELF_SERVICE);
        $currentUser->setId(222);
        $currentUser->getOrganisationUsers()->add($organisationUser);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('getIdentity->getUser')
            ->andReturn($currentUser);

        $this->commonHandleQueryTest();
    }

    /**
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\BadRequestException
     */
    public function testHandleQueryThrowsIncorrectUserTypeException()
    {
        /** @var TeamEntity $user */
        $team = m::mock(Team::class)->makePartial();

        /** @var UserEntity $currentUser */
        $currentUser = m::mock(UserEntity::class)->makePartial();
        $currentUser->setId(222);
        $currentUser->setTeam($team);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('getIdentity->getUser')
            ->andReturn($currentUser);

        $this->commonHandleQueryTest();
    }

    /**
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\BadRequestException
     */
    public function testHandleQueryThrowsIncorrectFilterException()
    {
        /** @var ContactDetailsEntity $partnerContactDetails */
        $partnerContactDetails = m::mock(ContactDetailsEntity::class)->makePartial();
        $partnerContactDetails->setId(1000);
        $partnerContactDetails->shouldReceive('getId')->once()->andReturn();

        /** @var UserEntity $currentUser */
        $currentUser = m::mock(UserEntity::class)->makePartial();
        $currentUser->setId(222);
        $currentUser->setPartnerContactDetails($partnerContactDetails);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('getIdentity->getUser')
            ->andReturn($currentUser);

        $this->commonHandleQueryTest();
    }
}
