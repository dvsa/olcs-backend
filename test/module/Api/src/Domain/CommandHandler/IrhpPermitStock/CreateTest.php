<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\IrhpPermitStock;

use Dvsa\Olcs\Api\Domain\Command\IrhpPermitSector\Create as CreateSectorQuotasCmd;
use Dvsa\Olcs\Api\Domain\Command\IrhpPermitJurisdiction\Create as CreateJurisdictionQuotasCmd;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Entity\ContactDetails\Country;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\IrhpPermitStock\Create as CreateHandler;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitStock as PermitStockRepo;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\IrhpPermitStock\Create as CreateCmd;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock as PermitStockEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;

/**
 * Create IRHP Permit Type Test
 *
 * @author Scott Callaway <scott.callaway@capgemini.com>
 */
class CreateTest extends CommandHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new CreateHandler();
        $this->mockRepo('IrhpPermitStock', PermitStockRepo::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            PermitStockEntity::STATUS_SCORING_NEVER_RUN,
        ];
        $this->references = [
            IrhpPermitType::class => [
                2 => m::mock(IrhpPermitType::class),
            ],
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $cmdData = [
            'irhpPermitType' => '2',
            'validFrom' => '2119-01-01',
            'validTo' => '2119-02-01',
            'initialStock' => '1500',
            'applicationPath' => 2,
            'businessProcess' => 'app_business_process_apg',
            'periodNameKey' => 'period.name.translation.key'
        ];

        $command = CreateCmd::create($cmdData);

        $this->repoMap['IrhpPermitStock']
            ->shouldReceive('save')
            ->once()
            ->with(m::type(PermitStockEntity::class))
            ->andReturnUsing(
                function (PermitStockEntity $permitStock) {
                    $permitStock->setId(1);
                }
            );

        $this->expectedSideEffect(CreateSectorQuotasCmd::class, ['id' => 1], new Result());
        $this->expectedSideEffect(CreateJurisdictionQuotasCmd::class, ['id' => 1], new Result());

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => ['IrhpPermitStock' => 1],
            'messages' => ["IRHP Permit Stock '1' created"]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    /**
     * Tests method on IrhpPermitStockTrait
     */
    public function testGoodValidityPeriodValidationEcmt()
    {
        $cmdData = [
            'initialStock' => 1000,
            'validFrom' => '2100-01-01',
            'validTo' => '2100-12-31',
            'irhpPermitType' => 1
        ];

        $cmd = CreateCmd::create($cmdData);
        $this->assertNull($this->sut->validityPeriodValidation($cmd));
    }

    /**
     * Tests method on IrhpPermitStockTrait
     */
    public function testGoodValidityPeriodValidationRemovals()
    {
        $cmdData = [
            'initialStock' => 1000,
            'validFrom' => null,
            'validTo' => null,
            'irhpPermitType' => 3
        ];

        $cmd = CreateCmd::create($cmdData);
        $this->assertNull($this->sut->validityPeriodValidation($cmd));
    }

    /**
     * Tests method on IrhpPermitStockTrait
     */
    public function testBadValidityPeriodValidationEcmt()
    {
        $cmdData = [
            'initialStock' => 1000,
            'validFrom' => null,
            'validTo' => null,
            'irhpPermitType' => 1
        ];

        $cmd = CreateCmd::create($cmdData);
        $this->expectException(ValidationException::class);
        $this->sut->validityPeriodValidation($cmd);
    }

    /**
     * Tests method on IrhpPermitStockTrait
     */
    public function testToBeforeFromValidityPeriodValidationEcmt()
    {
        $cmdData = [
            'initialStock' => 1000,
            'validFrom' => '2100-01-01',
            'validTo' => '2000-12-31',
            'irhpPermitType' => 1
        ];

        $cmd = CreateCmd::create($cmdData);
        $this->expectException(ValidationException::class);
        $this->sut->validityPeriodValidation($cmd);
    }
}
