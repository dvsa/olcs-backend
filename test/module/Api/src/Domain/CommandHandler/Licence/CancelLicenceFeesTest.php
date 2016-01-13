<?php

/**
 * Create Fee Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Fee;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Fee\CancelFee as CancelFeeCmd;
use Dvsa\Olcs\Api\Domain\Command\Licence\CancelLicenceFees as Cmd;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Licence\CancelLicenceFees;
use Dvsa\Olcs\Api\Domain\Repository\Licence;
use Dvsa\Olcs\Api\Entity\Fee\Fee;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

/**
 * Create Fee Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CancelLicenceFeesTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CancelLicenceFees();
        $this->mockRepo('Licence', Licence::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            Fee::STATUS_CANCELLED
        ];

        parent::initReferences();
    }

    public function testHandleCommandWithoutFees()
    {
        $data = [

        ];

        $command = Cmd::create($data);

        /** @var LicenceEntity $licence */
        $licence = m::mock(LicenceEntity::class)->makePartial();

        $fees = [];

        $licence->shouldReceive('getFees->matching')
            ->with(m::type(Criteria::class))
            ->andReturnUsing(
                function (Criteria $criteria) use ($fees) {
                    $expression = $criteria->getWhereExpression();

                    $this->assertEquals('feeStatus', $expression->getField());
                    $this->assertEquals('IN', $expression->getOperator());
                    $this->assertEquals([Fee::STATUS_OUTSTANDING], $expression->getValue()->getValue());

                    return $fees;
                }
            );

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT)
            ->andReturn($licence);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'No fees to remove'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommand()
    {
        $data = [

        ];

        $command = Cmd::create($data);

        /** @var LicenceEntity $licence */
        $licence = m::mock(LicenceEntity::class)->makePartial();

        $fees = [
            m::mock(Fee::class)->shouldReceive('getId')->andReturn(123)->getMock(),
            m::mock(Fee::class)->shouldReceive('getId')->andReturn(124)->getMock(),
        ];

        $licence->shouldReceive('getFees->matching')
            ->with(m::type(Criteria::class))
            ->andReturnUsing(
                function (Criteria $criteria) use ($fees) {
                    $expression = $criteria->getWhereExpression();

                    $this->assertEquals('feeStatus', $expression->getField());
                    $this->assertEquals('IN', $expression->getOperator());
                    $this->assertEquals([Fee::STATUS_OUTSTANDING], $expression->getValue()->getValue());

                    return $fees;
                }
            );

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT)
            ->andReturn($licence);

        $this->expectedSideEffect(
            CancelFeeCmd::class,
            ['id' => 123],
            (new Result())->addMessage('Fee 123 cancelled successfully')
        );
        $this->expectedSideEffect(
            CancelFeeCmd::class,
            ['id' => 124],
            (new Result())->addMessage('Fee 124 cancelled successfully')
        );

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'Fee 123 cancelled successfully',
                'Fee 124 cancelled successfully',
                '2 fee(s) cancelled'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
