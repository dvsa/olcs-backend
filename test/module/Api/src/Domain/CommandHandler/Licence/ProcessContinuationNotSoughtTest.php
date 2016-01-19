<?php

/**
 * Process Continuation Not Sought test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Licence;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Discs\CeaseGoodsDiscs;
use Dvsa\Olcs\Api\Domain\Command\Discs\CeasePsvDiscs;
use Dvsa\Olcs\Api\Domain\Command\Licence\ExpireAllCommunityLicences;
use Dvsa\Olcs\Api\Domain\Command\Licence\ProcessContinuationNotSought as Command;
use Dvsa\Olcs\Api\Domain\Command\LicenceVehicle\RemoveLicenceVehicle;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Command\Tm\DeleteTransportManagerLicence;
use Dvsa\Olcs\Api\Domain\Command\Publication\Licence as PublicationLicenceCmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\Licence\ProcessContinuationNotSought as Sut;
use Dvsa\Olcs\Api\Domain\Repository\Licence as Repo;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

/**
 * Process Continuation Not Sought test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class ProcessContinuationNotSoughtTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new Sut();
        $this->mockRepo('Licence', Repo::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            LicenceEntity::LICENCE_STATUS_CONTINUATION_NOT_SOUGHT,
        ];

        $this->references = [];

        parent::initReferences();
    }

    public function testHandleCommandGoods()
    {
        $licenceId = 69;
        $version = 2;

        $command = Command::create(['id' => $licenceId, 'version' => $version]);

        $licenceVehicles = [];
        $licence = m::mock(LicenceEntity::class)
            ->shouldReceive('getId')
            ->andReturn($licenceId)
            ->shouldReceive('isGoods')
            ->andReturn(true)
            ->shouldReceive('getLicenceVehicles')
            ->andReturn($licenceVehicles)
            ->shouldReceive('setStatus')
            ->once()
            ->with($this->mapRefData(LicenceEntity::LICENCE_STATUS_CONTINUATION_NOT_SOUGHT))
            ->getMock();

        $this->repoMap['Licence']
            ->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT, $version)
            ->once()
            ->andReturn($licence)
            ->shouldReceive('save')
            ->with($licence)
            ->once();

        $this->expectedSideEffect(
            CeaseGoodsDiscs::class,
            ['licence' => 69],
            (new Result())->addMessage('Goods discs ceased')
        );

        $this->expectedSideEffect(
            ExpireAllCommunityLicences::class,
            ['id' => $licenceId],
            (new Result())->addMessage('Community licences expired')
        );

        $this->expectedSideEffect(
            RemoveLicenceVehicle::class,
            ['licence' => 69],
            (new Result())->addMessage('Licence vehicles removed')
        );

        $this->expectedSideEffect(
            DeleteTransportManagerLicence::class,
            ['licence' => $licenceId],
            (new Result())->addMessage('Removed transport managers for licence')
        );

        $this->expectedSideEffect(
            PublicationLicenceCmd::class,
            ['id' => $licenceId],
            new Result()
        );

        $response = $this->sut->handleCommand($command);

        $this->assertEquals(
            [
                'Licence vehicles removed',
                'Removed transport managers for licence',
                'Community licences expired',
                'Goods discs ceased',
                'Licence updated'
            ],
            $response->getMessages()
        );
    }

    public function testHandleCommandPsv()
    {
        $licenceId = 69;
        $version = 2;

        $command = Command::create(['id' => $licenceId, 'version' => $version]);

        $psvDiscs = ['PSV_DISCS'];
        $licenceVehicles = ['LICENCE_VEHICLES'];

        $licence = m::mock(LicenceEntity::class)
            ->shouldReceive('getId')
            ->andReturn($licenceId)
            ->shouldReceive('isGoods')
            ->andReturn(false)
            ->shouldReceive('getPsvDiscs')
            ->andReturn($psvDiscs)
            ->shouldReceive('getLicenceVehicles')
            ->andReturn($licenceVehicles)
            ->shouldReceive('setStatus')
            ->once()
            ->with($this->mapRefData(LicenceEntity::LICENCE_STATUS_CONTINUATION_NOT_SOUGHT))
            ->getMock();

        $this->repoMap['Licence']
            ->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT, $version)
            ->once()
            ->andReturn($licence)
            ->shouldReceive('save')
            ->with($licence)
            ->once();

        $this->expectedSideEffect(
            CeasePsvDiscs::class,
            ['discs' => $psvDiscs],
            (new Result())->addMessage('PSV discs ceased')
        );

        $this->expectedSideEffect(
            ExpireAllCommunityLicences::class,
            ['id' => $licenceId],
            (new Result())->addMessage('Community licences expired')
        );

        $this->expectedSideEffect(
            RemoveLicenceVehicle::class,
            ['licence' => 69],
            (new Result())->addMessage('Licence vehicles removed')
        );

        $this->expectedSideEffect(
            DeleteTransportManagerLicence::class,
            ['licence' => $licenceId],
            (new Result())->addMessage('Removed transport managers for licence')
        );

        $this->expectedSideEffect(
            PublicationLicenceCmd::class,
            ['id' => $licenceId],
            new Result()
        );

        $response = $this->sut->handleCommand($command);

        $this->assertEquals(
            [
                'Licence vehicles removed',
                'Removed transport managers for licence',
                'Community licences expired',
                'PSV discs ceased',
                'Licence updated'
            ],
            $response->getMessages()
        );
    }
}
