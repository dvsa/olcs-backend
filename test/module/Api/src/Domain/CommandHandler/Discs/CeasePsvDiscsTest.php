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
        $this->mockRepo('PsvDisc', DiscRepo::class);
        parent::setUp();
    }

    public function testHandleCommand()
    {
        $data = [
            'discs' => [
                m::mock(PsvDisc::class)->makePartial()
            ]
        ];

        $command = Cmd::create($data);

        $this->repoMap['PsvDisc']
            ->shouldReceive('save')
            ->once()
            ->with(m::type(PsvDisc::class));

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'Ceased 1 discs for licence.'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
