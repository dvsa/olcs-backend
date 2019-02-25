<?php


namespace Dvsa\OlcsTest\Api\Domain\Validation\Validators;

use Dvsa\Olcs\Api\Domain\Validation\Validators\CanConfirmSurrender;
use Dvsa\Olcs\Api\Entity\Surrender;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use \Mockery as m;

class CanConfirmSurrenderTest extends AbstractValidatorsTestCase
{
    protected $sut;

    public function setUp()
    {
        $this->sut = new CanConfirmSurrender();
        parent::setUp();
    }

    /**
     * @dataProvider surrenderStates
     */
    public function testIsValid($status, $expected)
    {
        $statusEntity = m::mock(RefData::class);
        $statusEntity->shouldReceive('getId')->andReturn($status);

        $surrenderEntity = m::mock(Surrender::class);
        $repo = $this->mockRepo('Surrender');

        if ($this->dataDescription() === 'signed surrender') {
            $this->setIsGranted(Permission::INTERNAL_USER, false);
            $this->auth->shouldReceive('getIdentity')->andReturn(null);
            $repo->shouldReceive('fetchById')->once()->andReturn($surrenderEntity);
            $this->setIsValid('isOwner', [$surrenderEntity], true);
            $surrenderEntity->shouldReceive('getId')->twice()->andReturn(1);
        } else {
            $surrenderEntity->shouldReceive('getId')->once()->andReturn(1);
        }

        $surrenderEntity->shouldReceive('getStatus')->andReturn($statusEntity);
        $repo->shouldReceive('fetchOneByLicenceId')->with(1)->andReturn($surrenderEntity);

        $this->assertSame($expected, $this->sut->isValid($surrenderEntity));
    }

    public function surrenderStates()
    {
        return [
            'signed surrender' => [
                Surrender::SURRENDER_STATUS_SIGNED,
                true
            ],
            'submitted surrender' => [
                Surrender::SURRENDER_STATUS_SUBMITTED,
                false
            ],
            'surrender incomplete' => [
                Surrender::SURRENDER_DOC_STATUS_DESTROYED,
                false
            ]
        ];
    }
}
