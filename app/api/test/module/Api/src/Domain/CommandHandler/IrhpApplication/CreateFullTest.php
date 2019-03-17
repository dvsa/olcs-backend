<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\IrhpApplication;

use Dvsa\Olcs\Api\Domain\CommandHandler\IrhpApplication\CreateFull as CreateHandler;
use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Domain\Repository\IrhpApplication as IrhpApplicationRepo;
use Dvsa\Olcs\Api\Entity\IrhpInterface;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;
use Dvsa\Olcs\Transfer\Command\IrhpApplication\CreateFull as CreateCmd;
use Dvsa\Olcs\Transfer\Command\IrhpApplication\UpdateCountries;
use Dvsa\Olcs\Transfer\Command\IrhpApplication\UpdateMultipleNoOfPermits;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Command\Result;

/**
 * Create Irhp Application test
 */
class CreateFullTest extends CommandHandlerTestCase
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
            IrhpInterface::SOURCE_INTERNAL,
            IrhpInterface::STATUS_NOT_YET_SUBMITTED,
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $permitTypeId = IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL;
        $licenceId = 2;

        $cmdData = [
            'irhpPermitType' => $permitTypeId,
            'licence' => $licenceId,
            'dateReceived' => '2019-01-01',
            'declaration' => 0,
            'countries' => ['DE', 'FR', 'NL'],
            'permitsRequired' => [
                'DE' => [
                    2019 => 1,
                    2020 => 1
                ],
                'FR' => [
                    2019 => 1,
                    2020 => 1
                ],
                'NL' => [
                    2020 => 1
                ],
            ]
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

        $result1 = new Result();
        $result1->addMessage('section updated');
        $sideEffectData = [
            'id' => 4,
            'countries' => array_keys($command->getPermitsRequired())
        ];
        $this->expectedSideEffect(UpdateCountries::class, $sideEffectData, $result1);

        $this->repoMap['IrhpApplication']
            ->shouldReceive('refresh')
            ->twice()
            ->andReturnSelf();

        $result2 = new Result();
        $result2->addMessage('section updated');
        $sideEffectData = [
            'id' => 4,
            'permitsRequired' => $command->getPermitsRequired()
        ];
        $this->expectedSideEffect(UpdateMultipleNoOfPermits::class, $sideEffectData, $result2);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'irhpApplication' => 4,
            ],
            'messages' => [
                0 => 'section updated',
                1 => 'section updated',
                2 => 'IRHP Application created successfully',
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandNoPermitTypeFound()
    {
        $permitTypeId = 999;
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
