<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\IrhpApplication;

use Dvsa\Olcs\Api\Domain\Command\IrhpApplication\CreateDefaultIrhpPermitApplications;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\IrhpApplication\Create as CreateHandler;
use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Domain\Repository\IrhpApplication as IrhpApplicationRepo;
use Dvsa\Olcs\Api\Entity\IrhpInterface;
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
                IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL => m::mock(IrhpPermitType::class)
            ],
            Licence::class => [
                2 => m::mock(Licence::class),
            ],
            IrhpApplication::class => [
                4 => m::mock(IrhpApplication::class),
            ],
        ];

        $this->refData = [
            IrhpInterface::SOURCE_SELFSERVE,
            IrhpInterface::STATUS_NOT_YET_SUBMITTED,
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $permitTypeId = IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL;
        $licenceId = 2;

        $this->repoMap['IrhpApplication']
            ->shouldReceive('save')
            ->with(m::type(IrhpApplication::class))
            ->once()
            ->andReturnUsing(
                function ($irhpApplication) {
                    $this->assertSame(
                        $this->refData[IrhpInterface::SOURCE_SELFSERVE],
                        $irhpApplication->getSource()
                    );

                    $this->assertSame(
                        $this->refData[IrhpInterface::STATUS_NOT_YET_SUBMITTED],
                        $irhpApplication->getStatus()
                    );

                    $this->assertSame(
                        $this->references[IrhpPermitType::class][IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL],
                        $irhpApplication->getIrhpPermitType()
                    );

                    $this->assertSame(
                        $this->references[Licence::class][2],
                        $irhpApplication->getLicence()
                    );

                    $this->assertEquals(
                        date('Y-m-d'),
                        $irhpApplication->getDateReceived(true)->format('Y-m-d')
                    );

                    $irhpApplication->setId(4);

                    return;
                }
            );

        $sideEffectResult = new Result();
        $sideEffectResult->addMessage('Message from CreateDefaultIrhpPermitApplications');

        $this->expectedSideEffect(
            CreateDefaultIrhpPermitApplications::class,
            ['id' => 4],
            $sideEffectResult
        );

        $command = CreateCmd::create(
            [
                'irhpPermitType' => $permitTypeId,
                'licence' => $licenceId
            ]
        );

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'irhpApplication' => 4,
            ],
            'messages' => [
                'Message from CreateDefaultIrhpPermitApplications',
                'IRHP Application created successfully'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandNoPermitTypeFound()
    {
        $permitTypeId = 2000;
        $licenceId = 2;

        $cmdData = [
            'irhpPermitType' => $permitTypeId,
            'licence' => $licenceId,
        ];

        $command = CreateCmd::create($cmdData);

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Permit type not found');

        $this->sut->handleCommand($command);
    }
}
