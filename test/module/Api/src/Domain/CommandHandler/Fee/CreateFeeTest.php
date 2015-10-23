<?php

/**
 * Create Fee Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Fee;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\CommandHandler\Fee\CreateFee;
use Dvsa\Olcs\Api\Domain\Command\Fee\CreateFee as Cmd;
use Dvsa\Olcs\Api\Domain\Repository\Fee;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\Fee\FeeType;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Task\Task;
use Dvsa\Olcs\Api\Entity\Bus\BusReg;
use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Entity\Fee\Fee as FeeEntity;

/**
 * Create Fee Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CreateFeeTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CreateFee();
        $this->mockRepo('Fee', Fee::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            FeeEntity::STATUS_OUTSTANDING
        ];

        $this->references = [
            FeeType::class => [
                99 => m::mock(FeeType::class),
                101 => m::mock(FeeType::class)
            ],
            Task::class => [
                11 => m::mock(Task::class)
            ],
            Application::class => [
                22 => m::mock(Application::class)
            ],
            Licence::class => [
                33 => m::mock(Licence::class)
            ],
            BusReg::class => [
                44 => m::mock(BusReg::class)
            ]
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $data = [
            'feeType' => 99,
            'feeStatus' => FeeEntity::STATUS_OUTSTANDING,
            'amount' => 10.5,
            'task' => 11,
            'application' => 22,
            'licence' => 33,
            'busReg' => 44,
            'invoicedDate' => '2015-01-01',
            'description' => 'Some fee'
        ];

        $this->mapReference(FeeType::class, 99)
            ->shouldReceive('isMiscellaneous')
            ->andReturn(false);

        /** @var FeeEntity $savedFee */
        $savedFee = null;

        $command = Cmd::create($data);

        $this->repoMap['Fee']->shouldReceive('save')
            ->once()
            ->with(m::type(FeeEntity::class))
            ->andReturnUsing(
                function (FeeEntity $fee) use (&$savedFee) {
                    $fee->setId(111);
                    $savedFee = $fee;
                }
            );

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'fee' => 111,
            ],
            'messages' => [
                'Fee created successfully'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());

        $this->assertEquals('Some fee', $savedFee->getDescription());
        $this->assertEquals('2015-01-01', $savedFee->getInvoicedDate()->format('Y-m-d'));
        $this->assertSame($this->references[Licence::class][33], $savedFee->getLicence());
        $this->assertSame($this->references[Application::class][22], $savedFee->getApplication());
        $this->assertSame($this->references[BusReg::class][44], $savedFee->getBusReg());
        $this->assertSame($this->references[Task::class][11], $savedFee->getTask());
        $this->assertEquals(10.5, $savedFee->getAmount());
        $this->assertSame($this->refData[FeeEntity::STATUS_OUTSTANDING], $savedFee->getFeeStatus());
        $this->assertSame($this->references[FeeType::class][99], $savedFee->getFeeType());
    }

    public function testHandleCommandForApplicationFee()
    {
        $data = [
            'feeType' => 101,
            'application' => 22,
            'invoicedDate' => '2015-01-01',
        ];

        $this->mapReference(FeeType::class, 101)
            ->shouldReceive('isMiscellaneous')
            ->andReturn(true)
            ->shouldReceive('getDescription')
            ->andReturn('some fee type')
            ->shouldReceive('getAmount')
            ->andReturn('123.45');

        $this->mapReference(Application::class, 22)
            ->setLicence($this->mapReference(Licence::class, 33));

        /** @var FeeEntity $savedFee */
        $savedFee = null;

        $command = Cmd::create($data);

        $this->repoMap['Fee']->shouldReceive('save')
            ->once()
            ->with(m::type(FeeEntity::class))
            ->andReturnUsing(
                function (FeeEntity $fee) use (&$savedFee) {
                    $fee->setId(111);
                    $savedFee = $fee;
                }
            );

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'fee' => 111,
            ],
            'messages' => [
                'Fee created successfully'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());

        $this->assertEquals('some fee type', $savedFee->getDescription());
        $this->assertEquals('2015-01-01', $savedFee->getInvoicedDate()->format('Y-m-d'));
        $this->assertSame($this->references[Licence::class][33], $savedFee->getLicence());
        $this->assertSame($this->references[Application::class][22], $savedFee->getApplication());
        $this->assertEquals('123.45', $savedFee->getAmount());
        $this->assertSame($this->refData[FeeEntity::STATUS_OUTSTANDING], $savedFee->getFeeStatus());
        $this->assertSame($this->references[FeeType::class][101], $savedFee->getFeeType());
    }

    public function testHandleCommandForBusRegFee()
    {
        $data = [
            'feeType' => 101,
            'busReg' => 44,
            'invoicedDate' => '2015-01-01',
        ];

        $this->mapReference(FeeType::class, 101)
            ->shouldReceive('isMiscellaneous')
            ->andReturn(true)
            ->shouldReceive('getDescription')
            ->andReturn('some fee type')
            ->shouldReceive('getAmount')
            ->andReturn('123.45');

        $this->mapReference(BusReg::class, 44)
            ->setLicence($this->mapReference(Licence::class, 33));

        /** @var FeeEntity $savedFee */
        $savedFee = null;

        $command = Cmd::create($data);

        $this->repoMap['Fee']->shouldReceive('save')
            ->once()
            ->with(m::type(FeeEntity::class))
            ->andReturnUsing(
                function (FeeEntity $fee) use (&$savedFee) {
                    $fee->setId(111);
                    $savedFee = $fee;
                }
            );

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'fee' => 111,
            ],
            'messages' => [
                'Fee created successfully'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());

        $this->assertEquals('some fee type', $savedFee->getDescription());
        $this->assertEquals('2015-01-01', $savedFee->getInvoicedDate()->format('Y-m-d'));
        $this->assertSame($this->references[Licence::class][33], $savedFee->getLicence());
        $this->assertSame($this->references[BusReg::class][44], $savedFee->getBusReg());
        $this->assertEquals('123.45', $savedFee->getAmount());
        $this->assertSame($this->refData[FeeEntity::STATUS_OUTSTANDING], $savedFee->getFeeStatus());
        $this->assertSame($this->references[FeeType::class][101], $savedFee->getFeeType());
    }
}
