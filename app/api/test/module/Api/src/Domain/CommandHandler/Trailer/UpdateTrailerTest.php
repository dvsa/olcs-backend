<?php

/**
 * UpdateTrailer.php
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Trailer;

use Doctrine\ORM\Query;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\CommandHandler\Trailer\UpdateTrailer;
use Dvsa\Olcs\Api\Domain\Repository\Trailer as TrailerRepo;
use Dvsa\Olcs\Api\Entity\Licence\Trailer;
use Dvsa\Olcs\Transfer\Command\Trailer\UpdateTrailer as Cmd;
use Mockery as m;

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

    /**
     * @dataProvider dpIsLongerSemiTrailer
     */
    public function testHandleCommand($isLongerSemiTrailer, $expectedIsLongerSemiTrailer)
    {
        $trailerId = 1;
        $trailerNo = 'A1000';
        $version = 3;

        $data = [
            'id' => $trailerId,
            'trailerNo' => $trailerNo,
            'isLongerSemiTrailer' => $isLongerSemiTrailer,
            'version' => $version
        ];

        $command = Cmd::create($data);

        $trailer = m::mock(Trailer::class);
        $trailer->shouldReceive('getId')
            ->withNoArgs()
            ->andReturn($trailerId);

        $this->repoMap['Trailer']->shouldReceive('fetchById')
            ->with($trailerId, Query::HYDRATE_OBJECT, $version)
            ->once()
            ->andReturn($trailer)
            ->globally()
            ->ordered();
        $trailer->shouldReceive('setTrailerNo')
            ->with($trailerNo)
            ->once()
            ->globally()
            ->ordered();
        $trailer->shouldReceive('setIsLongerSemiTrailer')
            ->with($expectedIsLongerSemiTrailer)
            ->once()
            ->globally()
            ->ordered();
        $this->repoMap['Trailer']->shouldReceive('save')
            ->with($trailer)
            ->once()
            ->globally()
            ->ordered();

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'trailer' => $trailerId
            ],
            'messages' => [
                'Trailer updated successfully'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function dpIsLongerSemiTrailer()
    {
        return [
            ['Y', true],
            ['N', false],
        ];
    }
}
