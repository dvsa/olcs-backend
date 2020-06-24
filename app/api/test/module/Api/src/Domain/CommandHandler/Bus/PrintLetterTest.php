<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Bus;

use Dvsa\Olcs\Api\Domain\Command as DomainCmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\Bus\PrintLetter;
use Dvsa\Olcs\Api\Domain\Exception\BadRequestException;
use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Entity;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Transfer\Command as TransferCmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

/**
 * @covers \Dvsa\Olcs\Api\Domain\CommandHandler\Bus\PrintLetter
 */
class PrintLetterTest extends CommandHandlerTestCase
{
    const LIC_ID = 9999;
    const BUS_REG_ID = 7777;

    public function setUp(): void
    {
        $this->sut = new PrintLetter();

        $this->mockRepo('Bus', Repository\Bus::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            Entity\Bus\BusReg::STATUS_REGISTERED,
            Entity\Bus\BusReg::STATUS_CANCELLED,
            'brvr_route',
        ];

        parent::initReferences();
    }

    public function testHandleCommandBusReg404()
    {
        $cmd = TransferCmd\Bus\PrintLetter::create([]);

        $this->repoMap['Bus']
            ->shouldReceive('fetchUsingId')->with($cmd)->once()->andReturnNull();

        //  expect
        $this->expectException(NotFoundException::class, 'Bus registration not found');

        //  call
        $this->sut->handleCommand($cmd);
    }

    public function testHandleCommandExcTemplate404()
    {
        $cmd = TransferCmd\Bus\PrintLetter::create([]);

        $mockBusReg = m::mock(Entity\Bus\BusReg::class);
        $mockBusReg->shouldReceive('getLicence->getId')->once()->andReturn(self::LIC_ID);
        $mockBusReg->shouldReceive('getStatus->getId')->once()->andReturn('unit_StatusNotFound');
        $mockBusReg->shouldReceive('getId')->once()->andReturn(self::BUS_REG_ID);
        $mockBusReg->shouldReceive('isShortNoticeRefused')->once()->andReturn(false);

        $this->repoMap['Bus']
            ->shouldReceive('fetchUsingId')->with($cmd)->once()->andReturn($mockBusReg);

        //  expect
        $this->expectException(BadRequestException::class, 'Template not found for bus registration');

        //  call
        $this->sut->handleCommand($cmd);
    }

    /**
     * @dataProvider dpTestHandleCommand
     */
    public function testHandleCommand($status, $isVariation, $isShortNoticeRefused, array $docCmdData)
    {
        $cmd = TransferCmd\Bus\PrintLetter::create(
            [
                'printCopiesCount' => 999,
            ]
        );

        $mockBusReg = m::mock(Entity\Bus\BusReg::class);
        $mockBusReg->shouldReceive('getLicence->getId')->once()->andReturn(self::LIC_ID);
        $mockBusReg->shouldReceive('getStatus->getId')->times(2)->andReturn($status);
        $mockBusReg->shouldReceive('getId')->once()->andReturn(self::BUS_REG_ID);
        $mockBusReg->shouldReceive('isShortNoticeRefused')->times(2)->andReturn($isShortNoticeRefused);

        if ($isVariation !== null) {
            $mockBusReg->shouldReceive('isVariation')->times(2)->andReturn($isVariation);
        }

        $this->repoMap['Bus']
            ->shouldReceive('fetchUsingId')->with($cmd)->once()->andReturn($mockBusReg);

        $cmdData =
            [
                'query' => [
                    'licence' => self::LIC_ID,
                    'busRegId' => self::BUS_REG_ID,
                ],
                'licence' => self::LIC_ID,
                'busReg' => self::BUS_REG_ID,
                'category' => Category::CATEGORY_BUS_REGISTRATION,
                'subCategory' => Category::BUS_SUB_CATEGORY_OTHER_DOCUMENTS,
                'isExternal' => false,
                'dispatch' => true,
                'printCopiesCount' => 999,
            ] +
            $docCmdData;

        $this->expectedSideEffect(DomainCmd\Document\GenerateAndStore::class, $cmdData, 'EXPECT');

        static::assertEquals('EXPECT', $this->sut->handleCommand($cmd));
    }

    public function dpTestHandleCommand()
    {
        return [
            [
                'status' => Entity\Bus\BusReg::STATUS_REGISTERED,
                'isVariation' => false,
                'isShortNoticeRefused' => false,
                'docCmdData' => [
                    'template' => Entity\Doc\Document::BUS_REG_NEW,
                    'description' => 'Bus registration letter',
                ],
            ],
            [
                'status' => Entity\Bus\BusReg::STATUS_REGISTERED,
                'isVariation' => false,
                'isShortNoticeRefused' => true,
                'docCmdData' => [
                    'template' => Entity\Doc\Document::BUS_REG_NEW_REFUSE_SHORT_NOTICE,
                    'description' => 'Bus registration letter with refused short notice',
                ],
            ],
            [
                'status' => Entity\Bus\BusReg::STATUS_REGISTERED,
                'isVariation' => true,
                'isShortNoticeRefused' => false,
                'docCmdData' => [
                    'template' => Entity\Doc\Document::BUS_REG_VARIATION,
                    'description' => 'Bus variation letter',
                ],
            ],
            [
                'status' => Entity\Bus\BusReg::STATUS_REGISTERED,
                'isVariation' => true,
                'isShortNoticeRefused' => true,
                'docCmdData' => [
                    'template' => Entity\Doc\Document::BUS_REG_VARIATION_REFUSE_SHORT_NOTICE,
                    'description' => 'Bus variation letter with refused short notice',
                ],
            ],
            [
                'status' => Entity\Bus\BusReg::STATUS_CANCELLED,
                'isVariation' => null,
                'isShortNoticeRefused' => false,
                'docCmdData' => [
                    'template' => Entity\Doc\Document::BUS_REG_CANCELLATION,
                    'description' => 'Bus cancelled letter',
                ],
            ],
            [
                'status' => Entity\Bus\BusReg::STATUS_CANCELLED,
                'isVariation' => null,
                'isShortNoticeRefused' => true,
                'docCmdData' => [
                    'template' => Entity\Doc\Document::BUS_REG_CANCELLATION_REFUSE_SHORT_NOTICE,
                    'description' => 'Bus cancelled letter with refused short notice',
                ],
            ],
        ];
    }
}
