<?php

/**
 * CeaseGoodsDiscsTest.php
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Discs;

use Dvsa\Olcs\Api\Entity\Licence\LicenceVehicle;
use Mockery as m;
use Doctrine\ORM\Query;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;

use Dvsa\Olcs\Api\Domain\Repository\GoodsDisc as DiscRepo;
use Dvsa\Olcs\Api\Domain\CommandHandler\Discs\CeaseGoodsDiscs;

use Dvsa\Olcs\Api\Entity\Vehicle\GoodsDisc;

use Dvsa\Olcs\Api\Domain\Command\Discs\CeaseGoodsDiscs as Cmd;

/**
 * Class CeaseGoodsDiscsTest
 *
 * @package Dvsa\OlcsTest\Api\Domain\CommandHandler\Discs
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
class CeaseGoodsDiscsTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CeaseGoodsDiscs();
        $this->mockRepo('GoodsDisc', DiscRepo::class);
        parent::setUp();
    }

    public function testHandleCommand()
    {
        $data = [
            'licenceVehicles' => [
                m::mock(LicenceVehicle::class)
                    ->shouldReceive('getGoodsDiscs')
                    ->andReturn(
                        [
                            m::mock(GoodsDisc::class)
                                ->shouldReceive('getCeasedDate')
                                ->andReturn(null)
                                ->once()
                                ->shouldReceive('setCeasedDate')
                                ->once()
                                ->shouldReceive('setIsInterim')
                                ->with(false)
                                ->once()
                                ->getMock()
                        ]
                    )->getMock()
            ]
        ];

        $command = Cmd::create($data);

        $this->repoMap['GoodsDisc']
            ->shouldReceive('save')
            ->once()
            ->with(m::type(GoodsDisc::class));

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'Ceased discs for licence.'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
