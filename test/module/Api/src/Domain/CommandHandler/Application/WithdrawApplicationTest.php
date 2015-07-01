<?php

/**
 * WithdrawApplication.php
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Licence;

use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\CommandHandler\Application\WithdrawApplication as CommandHandler;
use Dvsa\Olcs\Transfer\Command\Application\WithdrawApplication as Command;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Domain\Command\Discs\CeaseGoodsDiscs;
use Dvsa\Olcs\Api\Domain\Command\Licence\Withdraw;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Domain\Command\Result;

/**
 * Withdraw Application Test
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
class WithdrawApplicationTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CommandHandler();
        $this->mockRepo('Application', Application::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            'apsts_withdrawn', 'withdrawn'
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $command = Command::create(['id' => 532, 'reason' => 'withdrawn']);

        $licence = m::mock(Licence::class)
            ->shouldReceive('getId')
            ->andReturn(123);

        $application = m::mock(Application::class)->makePartial();
        $application->setId(1);
        $application->setLicence($licence->getMock());

        $application->shouldReceive('getIsVariation')->andReturn(false);

        $this->repoMap['Application']->shouldReceive('fetchById')
            ->with(532)
            ->andReturn($application);

        $this->repoMap['Application']->shouldReceive('save')
            ->once()
            ->with(m::type(Application::class));

        $withdrawResult = new Result();
        $this->expectedSideEffect(Withdraw::class, ['id' => 123], $withdrawResult);

        $discsResult = new Result();
        $this->expectedSideEffect(
            CeaseGoodsDiscs::class,
            [
                'licenceVehicles' => null,
                'id' => null
            ],
            $discsResult
        );

        $result = $this->sut->handleCommand($command);

        $this->assertSame(["Application 1 withdrawn."], $result->getMessages());
    }
}
