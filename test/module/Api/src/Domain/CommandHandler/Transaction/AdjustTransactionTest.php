<?php

/**
 * Adjust Transaction Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Transaction;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Domain\Command\Fee\ResetFees as ResetFeesCmd;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Transaction\AdjustTransaction;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\Fee\Fee as FeeEntity;
use Dvsa\Olcs\Api\Entity\Fee\FeeTransaction as FeeTransactionEntity;
use Dvsa\Olcs\Api\Entity\Fee\Transaction as TransactionEntity;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Service\CpmsHelperInterface as CpmsHelper;
use Dvsa\Olcs\Transfer\Command\Transaction\AdjustTransaction as Cmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;
use ZfcRbac\Service\AuthorizationService;

/**
 * Adjust Transaction Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class AdjustTransactionTest extends CommandHandlerTestCase
{
    protected $mockCpmsService;

    protected $mockFeesHelperService;

    public function setUp()
    {
        $this->mockCpmsService = m::mock(CpmsHelper::class);
        $this->mockFeesHelperService = m::mock(FeesHelper::class);
        $this->mockedSmServices = [
            'CpmsHelperService' => $this->mockCpmsService,
            'FeesHelperService' => $this->mockFeesHelperService,
            AuthorizationService::class => m::mock(AuthorizationService::class)->makePartial(),
            'Config' => [],
        ];

        $this->sut = new AdjustTransaction();
        $this->mockRepo('Fee', Repository\Fee::class);
        $this->mockRepo('Transaction', Repository\Transaction::class);

        /** @var UserEntity $mockUser */
        $mockUser = m::mock(UserEntity::class)
            ->shouldReceive('getLoginId')
            ->andReturn('bob')
            ->getMock();

        $this->mockedSmServices[AuthorizationService::class]
            ->shouldReceive('getIdentity->getUser')
            ->andReturn($mockUser);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            TransactionEntity::STATUS_COMPLETE,
            FeeEntity::STATUS_OUTSTANDING => m::mock(RefData::class)
                ->makePartial()
                ->shouldReceive('getDescription')
                ->andReturn('Outstanding')
                ->getMock(),
            FeeEntity::METHOD_REVERSAL,
            FeeEntity::METHOD_CHEQUE,
            FeeEntity::METHOD_CASH,
            FeeEntity::METHOD_POSTAL_ORDER,
            FeeEntity::METHOD_CARD_ONLINE,
            FeeEntity::METHOD_CARD_OFFLINE,
        ];

        $this->references = [
            TransactionEntity::class => [
                123 => m::mock(TransactionEntity::class),
            ],
            FeeEntity::class => [
                69 => m::mock(FeeEntity::class),
            ],
            FeeTransactionEntity::class => [
                101 => m::mock(FeeTransactionEntity::class),
            ],
        ];

        parent::initReferences();
    }

    /**
     * Test validation (command is invalid if no details have changed)
     *
     * @param  Cmd               $command
     * @param  TransactionEntity $transaction
     * @param  boolean           $expected
     * @dataProvider validateChangesProvider
     */
    public function testValidate(Cmd $command, $expected)
    {
        if ($expected === false) {
            $this->setExpectedException(ValidationException::class);
        }

        $transaction = m::mock(TransactionEntity::class)
            ->shouldReceive('getTotalAmount')->andReturn('100.00')
            ->shouldReceive('getPayerName')->andReturn('Dan')
            ->shouldReceive('getPayingInSlipNumber')->andReturn('1234')
            ->shouldReceive('getChequePoNumber')->andReturn('2345')
            ->shouldReceive('getChequePoDate')->andReturn('2015-12-09')
            ->getMock();

        $result = $this->sut->validate($command, $transaction);

        $this->assertSame($expected, $result);
    }

    public function validateChangesProvider()
    {
        return [
            'no changes' => [
                Cmd::create(
                    [
                        'received' => '100.00',
                        'payer' => 'Dan',
                        'slipNo' => '1234',
                        'chequeNo' => '2345',
                        'poNo' => '2345',
                        'chequeDate' => '2015-12-09',
                    ]
                ),
                false,
            ],
            'amount changed' => [
                Cmd::create(
                    [
                        'received' => '200.00',
                        'payer' => 'Dan',
                        'slipNo' => '1234',
                        'chequeNo' => '2345',
                        'poNo' => '2345',
                        'chequeDate' => '2015-12-09',
                    ]
                ),
                true,
            ],
            'payer changed' => [
                Cmd::create(
                    [
                        'received' => '100.00',
                        'payer' => 'Bob',
                        'slipNo' => '1234',
                        'chequeNo' => '2345',
                        'poNo' => '2345',
                        'chequeDate' => '2015-12-09',
                    ]
                ),
                true,
            ],
            'slip no changed' => [
                Cmd::create(
                    [
                        'received' => '100.00',
                        'payer' => 'Dan',
                        'slipNo' => '1235',
                        'chequeNo' => '2345',
                        'poNo' => '2345',
                        'chequeDate' => '2015-12-09',
                    ]
                ),
                true,
            ],
            'cheque no changed' => [
                Cmd::create(
                    [
                        'received' => '100.00',
                        'payer' => 'Dan',
                        'slipNo' => '1234',
                        'chequeNo' => '2346',
                        'poNo' => '2345',
                        'chequeDate' => '2015-12-09',
                    ]
                ),
                true,
            ],
            'PO no changed' => [
                Cmd::create(
                    [
                        'received' => '100.00',
                        'payer' => 'Dan',
                        'slipNo' => '1234',
                        'chequeNo' => '2345',
                        'poNo' => '2346',
                        'chequeDate' => '2015-12-09',
                    ]
                ),
                true,
            ],
            'cheque date changed' => [
                Cmd::create(
                    [
                        'received' => '100.00',
                        'payer' => 'Dan',
                        'slipNo' => '1234',
                        'chequeNo' => '2345',
                        'poNo' => '2345',
                        'chequeDate' => '2015-12-01',
                    ]
                ),
                true,
            ],
        ];
    }
}
