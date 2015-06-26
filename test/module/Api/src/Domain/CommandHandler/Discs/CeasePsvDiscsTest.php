<?php

/**
 * CeasePsvDiscsTest.php
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Discs;

use Mockery as m;
use Doctrine\ORM\Query;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;

use Dvsa\Olcs\Api\Domain\Repository\PsvDisc as DiscRepo;
use Dvsa\Olcs\Api\Domain\CommandHandler\Discs\CeasePsvDiscs;

use Dvsa\Olcs\Api\Entity\Licence\PsvDisc;

use Dvsa\Olcs\Api\Domain\Command\Discs\CeasePsvDiscs as Cmd;

/**
 * Class CeasePsvDiscsTest
 *
 * @package Dvsa\OlcsTest\Api\Domain\CommandHandler\Discs
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
class CeasePsvDiscsTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CeasePsvDiscs();
        $this->mockRepo('PsvDiscs', DiscRepo::class);
        parent::setUp();
    }

    public function testHandleCommand()
    {
        $data = [
            'licence' => m::mock()->shouldReceive('getDiscs')
                ->andReturn(
                    [
                        m::mock(PsvDisc::class)->makePartial()
                    ]
                )
                ->getMock()
        ];

        $command = Cmd::create($data);

        $this->repoMap['PsvDiscs']
            ->shouldReceive('save')
            ->once()
            ->with(m::type(PsvDisc::class));

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
