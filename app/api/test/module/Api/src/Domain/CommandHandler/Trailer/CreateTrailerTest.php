<?php

/**
 * CreateTrailerTest.php
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Licence;

use Mockery as m;
use Doctrine\ORM\Query;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;

use Dvsa\Olcs\Api\Domain\Repository\Trailer as TrailerRepo;
use Dvsa\Olcs\Api\Domain\Repository\Licence as LicenceRepo;
use Dvsa\Olcs\Api\Domain\CommandHandler\Trailer\CreateTrailer;

use Dvsa\Olcs\Api\Entity\Licence\Trailer;

use Dvsa\Olcs\Transfer\Command\Trailer\CreateTrailer as Cmd;

/**
 * Class UpdateTypeOfLicenceTest
 *
 * @package Dvsa\OlcsTest\Api\Domain\CommandHandler\Licence
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
class CreateTrailerTest extends CommandHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new CreateTrailer();
        $this->mockRepo('Trailer', TrailerRepo::class);
        $this->mockRepo('Licence', LicenceRepo::class);

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $data = [
            'trailerNo' => 'A1000',
            'licence' => '7',
            'specifiedDate' => '2015-01-01'
        ];

        $command = Cmd::create($data);

        $this->repoMap['Trailer']
            ->shouldReceive('save')
            ->once()
            ->with(m::type(Trailer::class))
            ->andReturnUsing(
                function (Trailer $trailer) use ($data) {
                    $this->assertEquals($trailer->getTrailerNo(), $data['trailerNo']);
                    $this->assertEquals($trailer->getSpecifiedDate()->format('Y-m-d'), $data['specifiedDate']);
                }
            );

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'trailer' => null
            ],
            'messages' => [
                'Trailer created successfully'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());

    }
}
