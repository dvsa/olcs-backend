<?php

/**
 * Reprint Disc Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Vehicle;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Command\Vehicle\CeaseActiveDiscs;
use Dvsa\Olcs\Api\Domain\Command\Vehicle\CreateGoodsDiscs;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Entity\Licence\LicenceVehicle;
use Dvsa\Olcs\Api\Entity\Vehicle\GoodsDisc;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\Vehicle\ReprintDisc;
use Dvsa\Olcs\Transfer\Command\Vehicle\ReprintDisc as Cmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;

/**
 * Reprint Disc Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ReprintDiscTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new ReprintDisc();
        $this->mockRepo('LicenceVehicle', Repository\LicenceVehicle::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [];

        $this->references = [];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $data = [
            'ids' => [
                111,
                333,
                222
            ]
        ];
        $command = Cmd::create($data);

        /** @var GoodsDisc $disc1 */
        $disc1 = m::mock(GoodsDisc::class)->makePartial();

        /** @var GoodsDisc $disc2 */
        $disc2 = m::mock(GoodsDisc::class)->makePartial();
        $disc2->setDiscNo(123);

        /** @var LicenceVehicle $licenceVehicle1 */
        $licenceVehicle1 = m::mock(LicenceVehicle::class)->makePartial();
        $licenceVehicle1->shouldReceive('getActiveDisc')->andReturn(null);
        /** @var LicenceVehicle $licenceVehicle2 */
        $licenceVehicle2 = m::mock(LicenceVehicle::class)->makePartial();
        $licenceVehicle2->shouldReceive('getActiveDisc')->andReturn($disc1);
        /** @var LicenceVehicle $licenceVehicle3 */
        $licenceVehicle3 = m::mock(LicenceVehicle::class)->makePartial();
        $licenceVehicle3->shouldReceive('getActiveDisc')->andReturn($disc2);

        $this->repoMap['LicenceVehicle']->shouldReceive('fetchById')
            ->with(111)
            ->andReturn($licenceVehicle1)
            ->shouldReceive('fetchById')
            ->with(222)
            ->andReturn($licenceVehicle2)
            ->shouldReceive('fetchById')
            ->with(333)
            ->andReturn($licenceVehicle3);

        $data['ids'] = [111, 333];
        $result1 = new Result();
        $result1->addMessage('2 Disc(s) Ceased');
        $this->expectedSideEffect(CeaseActiveDiscs::class, $data, $result1);

        $result2 = new Result();
        $result2->addMessage('2 Disc(s) Created');
        $data['isCopy'] = 'Y';
        $this->expectedSideEffect(CreateGoodsDiscs::class, $data, $result2);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                '2 Disc(s) Ceased',
                '2 Disc(s) Created'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
