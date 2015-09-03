<?php

/**
 * Update Trailers Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Licence;

use Dvsa\Olcs\Api\Domain\CommandHandler\Licence\UpdateTrailers;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Transfer\Command\Licence\UpdateTrailers as Cmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

/**
 * Update Trailers Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class UpdateTrailersTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new UpdateTrailers();
        $this->mockRepo('Licence', Repository\Licence::class);

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $data = [
            'id' => 111,
            'shareInfo' => 'Y'
        ];

        $command = Cmd::create($data);

        $licence = m::mock(Licence::class);
        $licence->shouldReceive('getOrganisation->setConfirmShareTrailerInfo')
            ->once()
            ->with('Y');

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($licence)
            ->shouldReceive('save')
            ->once()
            ->with($licence);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'Organisation updated'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
