<?php

/**
 * Update Type of Licence Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Licence;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\CommandHandler\Licence\UpdateTypeOfLicence;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Transfer\Command\Licence\UpdateTypeOfLicence as Cmd;
use Dvsa\Olcs\Api\Domain\Repository\Licence;
use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use ZfcRbac\Service\AuthorizationService;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Domain\Exception\RequiresVariationException;

/**
 * Update Type of Licence Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class UpdateTypeOfLicenceTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new UpdateTypeOfLicence();
        $this->mockRepo('Licence', Licence::class);

        $this->mockedSmServices[AuthorizationService::class] = m::mock(AuthorizationService::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            LicenceEntity::LICENCE_TYPE_STANDARD_NATIONAL,
            LicenceEntity::LICENCE_TYPE_STANDARD_INTERNATIONAL,
            LicenceEntity::LICENCE_TYPE_SPECIAL_RESTRICTED,
            LicenceEntity::LICENCE_CATEGORY_GOODS_VEHICLE,
            LicenceEntity::LICENCE_CATEGORY_PSV
        ];

        parent::initReferences();
    }

    public function testHandleCommandWithoutChange()
    {
        $data = [
            'licenceType' => LicenceEntity::LICENCE_TYPE_STANDARD_NATIONAL,
            'version' => 1
        ];
        $command = Cmd::create($data);

        /** @var LicenceEntity $licence */
        $licence = m::mock(LicenceEntity::class)->makePartial();
        $licence->setLicenceType($this->refData[LicenceEntity::LICENCE_TYPE_STANDARD_NATIONAL]);

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($licence);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'No updates required'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandWithChangeWhenNotAllowed()
    {
        $this->setExpectedException(ForbiddenException::class);

        $data = [
            'licenceType' => LicenceEntity::LICENCE_TYPE_STANDARD_INTERNATIONAL,
            'version' => 1
        ];
        $command = Cmd::create($data);

        /** @var LicenceEntity $licence */
        $licence = m::mock(LicenceEntity::class)->makePartial();
        $licence->setLicenceType($this->refData[LicenceEntity::LICENCE_TYPE_STANDARD_NATIONAL]);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('isGranted')
            ->with(Permission::CAN_UPDATE_LICENCE_LICENCE_TYPE, $licence)
            ->andReturn(false);

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($licence);

        $this->sut->handleCommand($command);
    }

    public function testHandleCommandWithSrChangeWithoutPermission()
    {
        $this->setExpectedException(ValidationException::class);

        $data = [
            'licenceType' => LicenceEntity::LICENCE_TYPE_SPECIAL_RESTRICTED,
            'version' => 1
        ];
        $command = Cmd::create($data);

        /** @var LicenceEntity $licence */
        $licence = m::mock(LicenceEntity::class)->makePartial();
        $licence->setLicenceType($this->refData[LicenceEntity::LICENCE_TYPE_STANDARD_NATIONAL]);
        $licence->setGoodsOrPsv($this->refData[LicenceEntity::LICENCE_CATEGORY_GOODS_VEHICLE]);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('isGranted')
            ->with(Permission::CAN_UPDATE_LICENCE_LICENCE_TYPE, $licence)
            ->andReturn(true);

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($licence);

        $this->sut->handleCommand($command);
    }

    public function testHandleCommandWithChangeSelfserve()
    {
        $this->setExpectedException(RequiresVariationException::class);

        $data = [
            'licenceType' => LicenceEntity::LICENCE_TYPE_STANDARD_NATIONAL,
            'version' => 1
        ];
        $command = Cmd::create($data);

        /** @var LicenceEntity $licence */
        $licence = m::mock(LicenceEntity::class)->makePartial();
        $licence->setLicenceType($this->refData[LicenceEntity::LICENCE_TYPE_STANDARD_INTERNATIONAL]);
        $licence->setGoodsOrPsv($this->refData[LicenceEntity::LICENCE_CATEGORY_GOODS_VEHICLE]);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('isGranted')
            ->with(Permission::CAN_UPDATE_LICENCE_LICENCE_TYPE, $licence)
            ->andReturn(true)
            ->shouldReceive('isGranted')
            ->with(Permission::INTERNAL_USER, null)
            ->andReturn(false);

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($licence);

        $this->sut->handleCommand($command);
    }

    public function testHandleCommandWithChangeInternal()
    {
        $data = [
            'licenceType' => LicenceEntity::LICENCE_TYPE_STANDARD_NATIONAL,
            'version' => 1
        ];
        $command = Cmd::create($data);

        /** @var LicenceEntity $licence */
        $licence = m::mock(LicenceEntity::class)->makePartial();
        $licence->setLicenceType($this->refData[LicenceEntity::LICENCE_TYPE_STANDARD_INTERNATIONAL]);
        $licence->setGoodsOrPsv($this->refData[LicenceEntity::LICENCE_CATEGORY_GOODS_VEHICLE]);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('isGranted')
            ->with(Permission::CAN_UPDATE_LICENCE_LICENCE_TYPE, $licence)
            ->andReturn(true)
            ->shouldReceive('isGranted')
            ->with(Permission::INTERNAL_USER, null)
            ->andReturn(true);

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($licence)
            ->shouldReceive('save')
            ->with($licence);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'Licence saved successfully'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());

        $this->assertEquals($this->refData[LicenceEntity::LICENCE_TYPE_STANDARD_NATIONAL], $licence->getLicenceType());
    }
}
