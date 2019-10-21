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
use Dvsa\Olcs\Transfer\Command\Permits\CreateEcmtPermitApplication;
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
                IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT => m::mock(IrhpPermitType::class),
                IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM => m::mock(IrhpPermitType::class),
                IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_REMOVAL => m::mock(IrhpPermitType::class),
                IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL => m::mock(IrhpPermitType::class),
                IrhpPermitType::IRHP_PERMIT_TYPE_ID_MULTILATERAL => m::mock(IrhpPermitType::class)
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
            IrhpInterface::SOURCE_INTERNAL,
            IrhpInterface::STATUS_NOT_YET_SUBMITTED,
        ];

        parent::initReferences();
    }

    /**
     * @dataProvider dpTestHandleCommand
     */
    public function testHandleCommand(
        $permitTypeId,
        $licenceId,
        $fromInternal,
        $expectedSource,
        $expectedSideEffect,
        $sideEffectMsg,
        $resultEntity,
        $expected,
        $times
    ) {
        $this->repoMap['IrhpApplication']
            ->shouldReceive('save')
            ->with(m::type(IrhpApplication::class))
            ->times($times)
            ->andReturnUsing(
                function ($irhpApplication) use ($permitTypeId, $expectedSource) {
                    $this->assertSame(
                        $this->refData[$expectedSource],
                        $irhpApplication->getSource()
                    );

                    $this->assertSame(
                        $this->refData[IrhpInterface::STATUS_NOT_YET_SUBMITTED],
                        $irhpApplication->getStatus()
                    );

                    $this->assertSame(
                        $this->references[IrhpPermitType::class][$permitTypeId],
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
        $sideEffectResult->addId($resultEntity, 4);
        $sideEffectResult->addMessage($sideEffectMsg);

        $this->expectedSideEffect(
            $expectedSideEffect,
            [],
            $sideEffectResult
        );

        $command = CreateCmd::create(
            [
                'irhpPermitType' => $permitTypeId,
                'licence' => $licenceId,
                'fromInternal' => $fromInternal
            ]
        );

        $result = $this->sut->handleCommand($command);
        $this->assertEquals($expected, $result->toArray());
    }

    public function dpTestHandleCommand()
    {
        $expectedIrhp = [
            'id' => [
                'irhpApplication' => 4,
            ],
            'messages' => [
                'Message from CreateDefaultIrhpPermitApplications',
                'IRHP Application created successfully'
            ]
        ];

        $expectedEcmt = [
            'id' => [
                'ecmtPermitApplication' => 4,
            ],
            'messages' => [
                'ECMT Permit Application created successfully'
            ]
        ];

        $ssSource = IrhpInterface::SOURCE_SELFSERVE;
        $inSource = IrhpInterface::SOURCE_INTERNAL;

        $irhpSideEffect = CreateDefaultIrhpPermitApplications::class;
        $ecmtSideEffect = CreateEcmtPermitApplication::class;
        $ecmtSideEffectMsg = 'ECMT Permit Application created successfully';
        $irhpSideEffectMsg = 'Message from CreateDefaultIrhpPermitApplications';

        return
            [
                [ 1, 2, 0, $ssSource, $ecmtSideEffect,  $ecmtSideEffectMsg, 'ecmtPermitApplication', $expectedEcmt, 0],
                [ 2, 2, 0, $ssSource, $irhpSideEffect,  $irhpSideEffectMsg, 'irhpApplication', $expectedIrhp, 1],
                [ 3, 2, 0, $ssSource, $irhpSideEffect,  $irhpSideEffectMsg, 'irhpApplication', $expectedIrhp, 1],
                [ 4, 2, 0, $ssSource, $irhpSideEffect,  $irhpSideEffectMsg, 'irhpApplication', $expectedIrhp, 1],
                [ 5, 2, 0, $ssSource, $irhpSideEffect,  $irhpSideEffectMsg, 'irhpApplication', $expectedIrhp, 1],
                [ 1, 2, 1, $inSource, $ecmtSideEffect,  $ecmtSideEffectMsg, 'ecmtPermitApplication', $expectedEcmt, 0],
                [ 2, 2, 1, $inSource, $irhpSideEffect,  $irhpSideEffectMsg, 'irhpApplication', $expectedIrhp, 1],
                [ 3, 2, 1, $inSource, $irhpSideEffect,  $irhpSideEffectMsg, 'irhpApplication', $expectedIrhp, 1],
                [ 4, 2, 1, $inSource, $irhpSideEffect,  $irhpSideEffectMsg, 'irhpApplication', $expectedIrhp, 1],
                [ 5, 2, 1, $inSource, $irhpSideEffect,  $irhpSideEffectMsg, 'irhpApplication', $expectedIrhp, 1],
            ];
    }

    public function testHandleCommandNoPermitTypeFound()
    {
        $permitTypeId = 2000;
        $licenceId = 2;

        $cmdData = [
            'irhpPermitType' => $permitTypeId,
            'licence' => $licenceId,
            'fromInternal' => 0
        ];

        $command = CreateCmd::create($cmdData);

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Permit type not found');

        $this->sut->handleCommand($command);
    }
}
