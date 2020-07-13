<?php

namespace Dvsa\OlcsTest\Api\Assertion\User;

use Dvsa\Olcs\Api\Assertion\User\ReadUserSelfserve as Sut;
use Dvsa\Olcs\Api\Entity\Organisation\OrganisationUser;
use Dvsa\Olcs\Api\Entity\User\User;
use Doctrine\Common\Collections\ArrayCollection;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use ZfcRbac\Service\AuthorizationService;

/**
 * Read User Selfserve Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ReadUserSelfserveTest extends MockeryTestCase
{
    protected $sut;

    protected $auth;

    public function setUp(): void
    {
        $this->sut = new Sut();
        $this->auth = m::mock(AuthorizationService::class);
    }

    /**
     * @dataProvider getAssertForOperatorDataProvider
     */
    public function testAssertForOperator(
        $currentUserType,
        $currentUserEntityId,
        $userType,
        $userEntityId,
        $expected
    ) {
        $currentUserOrgUser = m::mock(OrganisationUser::class);
        $currentUserOrgUser->shouldReceive('getOrganisation->getId')->andReturn($currentUserEntityId);

        $currentUser = m::mock(User::class)->makePartial();
        $currentUser->shouldReceive('getUserType')->andReturn($currentUserType);
        $currentUser->shouldReceive('getOrganisationUsers')->andReturn(new ArrayCollection([$currentUserOrgUser]));

        $userOrgUser = m::mock(OrganisationUser::class);
        $userOrgUser->shouldReceive('getOrganisation->getId')->andReturn($userEntityId);

        $user = m::mock(User::class)->makePartial();
        $user->shouldReceive('getUserType')->andReturn($userType);
        $user->shouldReceive('getOrganisationUsers')->andReturn(new ArrayCollection([$userOrgUser]));

        $this->auth->shouldReceive('getIdentity->getUser')->andReturn($currentUser);

        $this->assertEquals($expected, $this->sut->assert($this->auth, $user));
    }

    public function getAssertForOperatorDataProvider()
    {
        return [
            // operator manages operator
            [User::USER_TYPE_OPERATOR, 123, User::USER_TYPE_OPERATOR, 123, true],
            [User::USER_TYPE_OPERATOR, 123, User::USER_TYPE_OPERATOR, 1, false],
            // operator manages TM
            [User::USER_TYPE_OPERATOR, 123, User::USER_TYPE_TRANSPORT_MANAGER, 123, true],
            [User::USER_TYPE_OPERATOR, 123, User::USER_TYPE_TRANSPORT_MANAGER, 1, false],
        ];
    }
}
