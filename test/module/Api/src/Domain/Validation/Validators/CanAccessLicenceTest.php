<?php

/**
 * Can Access Licence Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Validation\Validators;

use Dvsa\Olcs\Api\Domain\Validation\Validators\CanAccessLicence;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Api\Entity\User\User;
use Mockery as m;
use ZfcRbac\Identity\IdentityInterface;

/**
 * Can Access Licence Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CanAccessLicenceTest extends AbstractValidatorsTestCase
{
    /**
     * @var CanAccessLicence
     */
    protected $sut;

    public function setUp()
    {
        $this->sut = new CanAccessLicence();

        parent::setUp();
    }

    /**
     * @dataProvider provider
     */
    public function testIsValid($isOwner, $status, $expected)
    {
        $this->setIsGranted(Permission::INTERNAL_USER, false);
        $this->auth->shouldReceive('getIdentity')->andReturn(null);
        $entity = m::mock(Licence::class);
        $status = new RefData($status);
        $entity->shouldReceive('getStatus')->andReturn($status);
        $repo = $this->mockRepo('Licence');
        $repo->shouldReceive('fetchById')->with(111)->andReturn($entity);

        $this->setIsValid('isOwner', [$entity], $isOwner);

        $this->assertEquals($expected, $this->sut->isValid(111));
    }

    /**
     * @dataProvider provider
     */
    public function testIsValidLicNo($isOwner, $status, $expected)
    {
        $this->setIsGranted(Permission::INTERNAL_USER, false);
        $this->auth->shouldReceive('getIdentity')->andReturn(null);
        $entity = m::mock(Licence::class);
        $status = new RefData($status);
        $entity->shouldReceive('getStatus')->andReturn($status);
        $repo = $this->mockRepo('Licence');
        $repo->shouldReceive('fetchByLicNo')->with('XY12345')->andReturn($entity);

        $this->setIsValid('isOwner', [$entity], $isOwner);

        $this->assertEquals($expected, $this->sut->isValid('XY12345'));
    }

    public function testIsValidInternal()
    {
        $this->setIsGranted(Permission::INTERNAL_USER, true);
        $this->assertEquals(true, $this->sut->isValid(111));
    }

    public function testIsValidSystem()
    {
        $this->setIsGranted(Permission::INTERNAL_USER, false);
        $mockUser = m::mock(User::class);
        $mockUser->shouldReceive('isSystemUser')->andReturn(true);
        $mockIdentity = m::mock(IdentityInterface::class);
        $mockIdentity->shouldReceive('getUser')->andReturn($mockUser);
        $this->auth->shouldReceive('getIdentity')->andReturn($mockIdentity);
        $this->assertEquals(true, $this->sut->isValid(111));
    }

    public function provider()
    {

        $cases = [];

        $acceptedStatuses = [
            Licence::LICENCE_STATUS_VALID,
            Licence::LICENCE_STATUS_SUSPENDED,
            Licence::LICENCE_STATUS_CURTAILED,
        ];

        $notAcceptedStatuses = [
            Licence::LICENCE_STATUS_UNDER_CONSIDERATION,
            Licence::LICENCE_STATUS_GRANTED,
            Licence::LICENCE_STATUS_SURRENDER_UNDER_CONSIDERATION,
            Licence::LICENCE_STATUS_SURRENDERED,
            Licence::LICENCE_STATUS_WITHDRAWN,
            Licence::LICENCE_STATUS_REFUSED,
            Licence::LICENCE_STATUS_REVOKED,
            Licence::LICENCE_STATUS_NOT_TAKEN_UP,
            Licence::LICENCE_STATUS_TERMINATED,
            Licence::LICENCE_STATUS_CONTINUATION_NOT_SOUGHT,
            Licence::LICENCE_STATUS_UNLICENSED,
            Licence::LICENCE_STATUS_CANCELLED,
        ];

        foreach ($acceptedStatuses as $acceptedStatus) {
            $caseIsOwner = [
                'isOwner' => true,
                'status' => $acceptedStatus,
                'expected' => true,
            ];

            $caseIsNotOwner = [
                'isOwner' => false,
                'status' => $acceptedStatus,
                'expected' => false,
            ];

            array_push($cases, $caseIsOwner);
            array_push($cases, $caseIsNotOwner);
        }

        foreach ($notAcceptedStatuses as $notAcceptedStatus) {
            $caseIsOwner = [
                'isOwner' => true,
                'status' => $notAcceptedStatus,
                'expected' => false,
            ];

            $caseIsNotOwner = [
                'isOwner' => false,
                'status' => $notAcceptedStatus,
                'expected' => false,
            ];

            array_push($cases, $caseIsOwner);
            array_push($cases, $caseIsNotOwner);
        }

        return $cases;
    }
}
