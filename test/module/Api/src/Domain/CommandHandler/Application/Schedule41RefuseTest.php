<?php

/**
 * Schedule41RefuseTest.php
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Api\Domain\Command\LicenceOperatingCentre\DisassociateS4;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Command\Schedule41\RefuseS4;

use Dvsa\Olcs\Api\Entity\Application\S4;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\CommandHandler\Application\Schedule41Refuse;
use Dvsa\Olcs\Transfer\Command\Application\Schedule41Refuse as Cmd;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Domain\Command\ApplicationOperatingCentre\DeleteApplicationOperatingCentre;
use Dvsa\Olcs\Api\Domain\Command\Cases\ConditionUndertaking\DeleteConditionUndertakingS4;

/**
 * Class Schedule41RefuseTest
 *
 * @package Dvsa\OlcsTest\Api\Domain\CommandHandler\Licence
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
class Schedule41RefuseTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new Schedule41Refuse();
        $this->mockRepo('Application', Application::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $data = [
            'id' => 1,
        ];

        $command = Cmd::create($data);

        $this->repoMap['Application']
            ->shouldReceive('fetchById')
            ->once()
            ->andReturn(
                m::mock(Application::class)
                    ->shouldReceive('getS4s')
                    ->once()
                    ->andReturn(
                        m::mock()->shouldReceive('matching')
                            ->andReturn(
                                [
                                    m::mock(S4::class)
                                        ->shouldReceive('getId')
                                        ->times(3)
                                        ->andReturn(1)
                                        ->getMock()
                                ]
                            )
                            ->getMock()
                    )
                    ->shouldReceive('getLicence')
                    ->andReturn(
                        m::mock()
                            ->shouldReceive('getOperatingCentres')->getMock()
                    )
                    ->getMock()
            );

        $this->expectedSideEffect(
            RefuseS4::class,
            [
                'id' => 1,
            ],
            new Result()
        );

        $this->expectedSideEffect(
            DeleteApplicationOperatingCentre::class,
            [
                's4' => 1,
            ],
            new Result()
        );

        $this->expectedSideEffect(
            DeleteConditionUndertakingS4::class,
            [
                's4' => 1,
            ],
            new Result()
        );

        $this->expectedSideEffect(
            DisassociateS4::class,
            [
                'licenceOperatingCentres' => null
            ],
            new Result()
        );

        $this->sut->handleCommand($command);
    }
}
