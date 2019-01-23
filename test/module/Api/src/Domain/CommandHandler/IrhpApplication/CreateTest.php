<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\IrhpApplication;

use Dvsa\Olcs\Api\Domain\CommandHandler\IrhpApplication\Create as CreateHandler;
use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Domain\Repository\IrhpApplication as IrhpApplicationRepo;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;
use Dvsa\Olcs\Transfer\Command\IrhpApplication\Create as CreateCmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

/**
 * Create Irhp Application test
 */
class CreateTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CreateHandler();
        $this->mockRepo('IrhpApplication', IrhpApplicationRepo::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->references = [
            IrhpPermitType::class => [
                1 => m::mock(IrhpPermitType::class)
            ],
            Licence::class => [
                2 => m::mock(Licence::class),
            ],
            IrhpApplication::class => [
                4 => m::mock(IrhpApplication::class),
            ],
        ];

        $this->refData = [
            CreateHandler::SOURCE_SELFSERVE,
            CreateHandler::STATUS_NOT_YET_SUBMITTED,
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $permitTypeId = 1;
        $licenceId = 2;

        $cmdData = [
            'type' => $permitTypeId,
            'licence' => $licenceId,
        ];

        $command = CreateCmd::create($cmdData);

        $irhpApplication = null;

        $this->repoMap['IrhpApplication']
            ->shouldReceive('save')
            ->with(m::type(IrhpApplication::class))
            ->once()
            ->andReturnUsing(
                function (IrhpApplication $app) use (&$irhpApplication) {
                    $irhpApplication = $app;
                    $app->setId(4);
                }
            );

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'irhpApplication' => 4,
            ],
            'messages' => [
                0 => 'IRHP Application created successfully',
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandNoPermitTypeFound()
    {
        $permitTypeId = 2000;
        $licenceId = 2;

        $cmdData = [
            'type' => $permitTypeId,
            'licence' => $licenceId,
        ];

        $command = CreateCmd::create($cmdData);

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Permit type not found');

        $this->sut->handleCommand($command);
    }
}
