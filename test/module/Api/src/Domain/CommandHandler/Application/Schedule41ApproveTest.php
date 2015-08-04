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

    public function testHandleCommand()
    {
        $data = [
            'id' => 1,
            'isTrueS4' => false
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
                                        ->once()
                                        ->andReturn(1)
                                        ->getMock()
                                ]
                            )
                            ->getMock()
                    )->getMock()
            );

        $this->expectedSideEffect(
            ApproveS4::class,
            [
                'id' => 1,
                'isTrueS4' => null,
                'status' => null
            ],
            new Result()
        );

        $this->sut->handleCommand($command);
    }
}
