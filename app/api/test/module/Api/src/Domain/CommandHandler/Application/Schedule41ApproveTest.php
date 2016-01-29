<?php

/**
 * Schedule41ApproveTest.php
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Application;

use Doctrine\Common\Collections\Criteria;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Command\Schedule41\ApproveS4;
use Dvsa\Olcs\Api\Entity\Application\S4;
use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\CommandHandler\Application\Schedule41Approve;
use Dvsa\Olcs\Transfer\Command\Application\Schedule41Approve as Cmd;
use Dvsa\Olcs\Api\Entity\Application\Application;

/**
 * Class Schedule41ApproveTest
 *
 * @package Dvsa\OlcsTest\Api\Domain\CommandHandler\Licence
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
class Schedule41ApproveTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new Schedule41Approve();
        $this->mockRepo('Application', Application::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        parent::initReferences();
    }

    /**
     * @dataProvider dataProviderTestHandleCommand
     */
    public function testHandleCommand($expectedSection, $isNew, $isNi, $isTrueS4)
    {
        $data = [
            'id' => 1,
            'trueS4' => $isTrueS4,
        ];

        $command = Cmd::create($data);

        $application = m::mock(Application::class);
        $application->shouldReceive('getS4s')->with()->once()->andReturn(
            m::mock()->shouldReceive('matching')->once()->andReturn(
                new \Doctrine\Common\Collections\ArrayCollection(
                    [
                        m::mock(S4::class)
                            ->shouldReceive('getId')
                            ->once()
                            ->andReturn(14)
                            ->getMock()
                    ]
                )
            )
            ->getMock()
        );
        $trafficArea = new \Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea();
        $trafficArea->setIsNi($isNi);
        $trafficArea->setId('B');
        $application->shouldReceive('getTrafficArea')->with()->andReturn($trafficArea);
        $application->shouldReceive('isNew')->with()->andReturn($isNew);
        $application->shouldReceive('getId')->with()->andReturn(1);

        $this->repoMap['Application']->shouldReceive('fetchById')->with(1)->once()->andReturn($application);

        $this->expectedSideEffect(
            ApproveS4::class,
            [
                'id' => 14,
                'isTrueS4' => ($isTrueS4 === 'Y') ? 'Y' : 'N',
                'status' => null
            ],
            new Result()
        );

        $this->expectedSideEffect(
            \Dvsa\Olcs\Transfer\Command\Publication\Application::class,
            [
                'id' => 1,
                'trafficArea' => 'B',
                'publicationSection' => $expectedSection
            ],
            new Result()
        );

        if ($isTrueS4 !== 'Y') {
            $this->expectedSideEffect(
                \Dvsa\Olcs\Api\Domain\Command\Application\CreateTexTask::class,
                ['id' => 1],
                new Result()
            );
        }

        $this->sut->handleCommand($command);
    }

    public function dataProviderTestHandleCommand()
    {
        return [
            // expectedSection, isNew, isNi, isTrueS4
            'New application' => [16, true, false, 'Y'],
            'New application2' => [16, true, false, 'N'],
            'New application2' => [16, true, false, null],
            'New application NI' => [29, true, true, 'Y'],
            'New application2 NI' => [29, true, true, 'N'],
            'New application2 NI' => [29, true, true, null],
            'Variation untrue S4' => [17, false, false, 'N'],
            'Variation untrue S4' => [17, false, false, null],
            'Variation untrue S4 NI' => [30, false, true, 'N'],
            'Variation untrue S4 NI' => [30, false, true, null],
            'Variation true S4' => [18, false, false, 'Y'],
            'Variation true S4 NI' => [31, false, true, 'Y'],
        ];
    }
}
