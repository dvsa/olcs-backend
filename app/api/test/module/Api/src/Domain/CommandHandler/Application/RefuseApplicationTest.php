<?php

/**
 * RefuseApplicationTest.php
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Licence;

use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\CommandHandler\Application\RefuseApplication as CommandHandler;
use Dvsa\Olcs\Transfer\Command\Application\RefuseApplication as Command;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Domain\Command\Discs\CeaseGoodsDiscs;
use Dvsa\Olcs\Api\Domain\Command\Licence\Refuse;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Domain\Command\Result;

/**
 * Refuse Application Test
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
class RefuseApplicationTest extends CommandHandlerTestCase
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
            'apsts_refused'
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $command = Command::create(['id' => 532]);

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
        $this->expectedSideEffect(Refuse::class, ['id' => 123], $withdrawResult);

        $discsResult = new Result();
        $this->expectedSideEffect(
            CeaseGoodsDiscs::class,
            [
                'licence' => $licence->getMock(),
                'id' => null
            ],
            $discsResult
        );

        $result = $this->sut->handleCommand($command);

        $this->assertSame(["Application 1 refused."], $result->getMessages());
    }
}
