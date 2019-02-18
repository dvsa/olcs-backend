<?php


namespace Dvsa\OlcsTest\Api\Domain\Validation\Validators;

use Dvsa\Olcs\Api\Domain\Validation\Validators\CanAccessLicenceForSurrender;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\User\Permission;
use \Mockery as m;

class CanAccessLicenceForSurrenderTest extends AbstractValidatorsTestCase
{
    public function setUp()
    {
        $this->sut = new CanAccessLicenceForSurrender();
        parent::setUp();
    }

    /**
     * @dataProvider dpLicencePermissions
     */
    public function testIsValidExternalUserLicenceOwner($permission, $isOwner, $licenceState, $expected)
    {
        $this->setIsGranted($permission, true);
        $this->auth->shouldReceive('getIdentity')->andReturn(null);
        $entity = m::mock(Licence::class);
        $entity->shouldReceive('getId')->once()->andReturn(111);

        switch ($this->dataDescription()) {
            case 'selfservice-user-owner':
                $this->setIsValid('isOwner', [$entity], $isOwner);
                $entity->shouldReceive('getStatus->getId')->once()->andReturn($licenceState);

                break;
            case 'selfservice-user-owner-not-surrendered':
                $this->setIsGranted(Permission::INTERNAL_USER, false);
                $this->setIsValid('isOwner', [$entity], $isOwner);
                $entity->shouldReceive('getStatus->getId')->once()->andReturn($licenceState);

                break;

            case 'internal-user-not-surrendered':
                $this->setIsGranted(Permission::SELFSERVE_USER, false);
                $this->setIsValid('isOwner', [$entity], $isOwner);
                break;
        }


        $repo = $this->mockRepo('Licence');
        $repo->shouldReceive('fetchById')->with(111)->andReturn($entity);
        $this->assertEquals($expected, $this->sut->isValid($entity));
    }

    public function dpLicencePermissions()
    {
        return [
            'selfservice-user-owner' => [
                Permission::SELFSERVE_USER,
                true,
                Licence::LICENCE_STATUS_SURRENDER_UNDER_CONSIDERATION,
                false
            ],
            'selfservice-user-owner-not-surrendered' => [
                Permission::SELFSERVE_USER,
                true,
                Licence::LICENCE_STATUS_VALID,
                true

            ],
            'internal-user-not-surrendered' => [
                Permission::INTERNAL_USER,
                false,
                Licence::LICENCE_STATUS_VALID,
                true
            ]
        ];
    }
}
