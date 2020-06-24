<?php

/**
 * UpdateTrailer.php
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Licence;

use Mockery as m;
use Doctrine\ORM\Query;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;

use Dvsa\Olcs\Api\Domain\Repository\Trailer as TrailerRepo;
use Dvsa\Olcs\Api\Domain\CommandHandler\Trailer\UpdateTrailer;

use Dvsa\Olcs\Api\Entity\Licence\Trailer;

use Dvsa\Olcs\Transfer\Command\Trailer\UpdateTrailer as Cmd;

/**
 * Class UpdateTrailer
 *
 * @package Dvsa\OlcsTest\Api\Domain\CommandHandler\Trailer
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
class UpdateTrailerTest extends CommandHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new UpdateTrailer();
        $this->mockRepo('Trailer', TrailerRepo::class);

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $data = [
            'id' => 1,
            'trailerNo' => 'A1000',
            'licence' => '7',
            'specifiedDate' => '2015-01-01'
        ];

        $command = Cmd::create($data);

        $this->repoMap['Trailer']
            ->shouldReceive('fetchById')
            ->once()
            ->andReturn(
                m::mock(Trailer::class)
                    ->shouldReceive('setTrailerNo')
                    ->once()
                    ->shouldReceive('getId')
                    ->once()
                    ->getMock()
            )
            ->shouldReceive('save')
            ->once()
            ->with(m::type(Trailer::class));

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'trailer' => null
            ],
            'messages' => [
                'Trailer updated successfully'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());

    }
}
