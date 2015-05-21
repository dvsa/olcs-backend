<?php

/**
 * Create Fee Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Fee;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\CommandHandler\Licence\CancelLicenceFees;
use Dvsa\Olcs\Api\Domain\Command\Licence\CancelLicenceFees as Cmd;
use Dvsa\Olcs\Api\Domain\Repository\Licence;
use Dvsa\Olcs\Api\Entity\Fee\Fee;
use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;

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
                    $this->assertEquals(
                        [Fee::STATUS_OUTSTANDING, Fee::STATUS_WAIVE_RECOMMENDED],
                        $expression->getValue()->getValue()
                    );

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
            m::mock(Fee::class)->makePartial()
                ->shouldReceive('setFeeStatus')->once()->with($this->refData[Fee::STATUS_CANCELLED])->getMock(),
            m::mock(Fee::class)->makePartial()
                ->shouldReceive('setFeeStatus')->once()->with($this->refData[Fee::STATUS_CANCELLED])->getMock()
        ];

        $licence->shouldReceive('getFees->matching')
            ->with(m::type(Criteria::class))
            ->andReturnUsing(
                function (Criteria $criteria) use ($fees) {
                    $expression = $criteria->getWhereExpression();

                    $this->assertEquals('feeStatus', $expression->getField());
                    $this->assertEquals('IN', $expression->getOperator());
                    $this->assertEquals(
                        [Fee::STATUS_OUTSTANDING, Fee::STATUS_WAIVE_RECOMMENDED],
                        $expression->getValue()->getValue()
                    );

                    return $fees;
                }
            );

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT)
            ->andReturn($licence)
            ->shouldReceive('save')
            ->once()
            ->with($licence);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                '2 fee(s) cancelled'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
