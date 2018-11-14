<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\IrhpPermit;

use DateTime;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\IrhpPermit\ReplacementIrhpPermit;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Domain\Query\IrhpPermitRange\ByPermitNumber as RangeByPermitNumber;
use Dvsa\Olcs\Api\Domain\Query\IrhpPermit\ByPermitNumber as PermitByPermitNumber;
use Dvsa\Olcs\Api\Entity\Permits\IrhpCandidatePermit;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermit;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitRange;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Transfer\Command\IrhpPermit\Replace;
use Dvsa\Olcs\Api\Domain\CommandHandler\IrhpPermit\Replace as ReplaceHandler;
use Mockery as m;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\CommandHandler\IrhpPermit\CreateReplacement as CreateReplacementHandler;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitRange as PermitRangeRepo;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;

/**
 * Create Replacement IRHP Permit Test
 *
 * @author Andy Newton <andy@vitri.ltd>
 */
class ReplaceTest extends CommandHandlerTestCase
{
    public function setUp()
    {

        $this->sut = m::mock(ReplaceHandler::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $this->mockRepo('IrhpPermit', PermitRangeRepo::class);
        $this->mockRepo('IrhpPermitRange', PermitRangeRepo::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            IrhpPermit::STATUS_CEASED,
            IrhpPermit::STATUS_ISSUED
        ];

        parent::initReferences();
    }

    /**
     * Test
     */
    public function testHandleCommand()
    {
        $cmdData = [
            'id' => 9,
            'replacementIrhpPermit' => 1245
        ];

        $command = Replace::create($cmdData);

        /** @var IrhpPermit $oldPermit */
        $oldPermit = m::mock(IrhpPermit::class)->makePartial();
        $oldPermit->setIrhpCandidatePermit(m::mock(IrhpCandidatePermit::class)->makePartial());
        $oldPermit->setStatus(new RefData(IrhpPermit::STATUS_ISSUED));
        /** @var IrhpPermitRange $oldRange */
        $oldRange = m::mock(IrhpPermitRange::class)->makePartial();
        /** @var IrhpPermitStock $oldStock */
        $oldStock = m::mock(IrhpPermitStock::class)->makePartial();
        $oldStock->setValidTo(new DateTime());
        $oldRange->setIrhpPermitStock($oldStock);
        $oldPermit->setIrhpPermitRange($oldRange);
        $oldPermit->setId($cmdData['id']);

        $this->repoMap['IrhpPermit']
            ->shouldReceive('fetchById')
            ->with($cmdData['id'])
            ->andReturn($oldPermit);

        $oldPermit->shouldReceive('getStatus->getId')
            ->andReturn(IrhpPermit::STATUS_ISSUED);

        $this->sut->shouldReceive('handleQuery')
            ->once()
            ->andReturnUsing(function ($query) {
                $targetRange = m::mock(IrhpPermitRange::class)->makePartial();
                $targetRange->setId(7);
                return [
                    $targetRange
                ];
            });

        $this->sut->shouldReceive('handleQuery')
            ->once()
            ->andReturn([]);

        $result = new Result();
        $createCmdData = [
            'replaces' => 9,
            'irhpPermitRange' => 7,
            'permitNumber' => $cmdData['replacementIrhpPermit']
        ];

        $this->expectedSideEffect(ReplacementIrhpPermit::class, $createCmdData, $result);

        $this->repoMap['IrhpPermit']
            ->shouldReceive('save')
            ->once()
            ->with(m::type(IrhpPermit::class))
            ->andReturnUsing(
                function (IrhpPermit $irhpPermit) use (&$savedIrhpPermit) {
                    $irhpPermit->setId(1);
                    $savedIrhpPermit = $irhpPermit;
                }
            );

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => ['IrhpPermit' => 1],
            'messages' => ['The replacement permit has been successfully issued and can now be printed']
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    /**
     * Tests exception thrown for wrong status
     *
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\ValidationException
     */
    public function testWrongStatusException()
    {
        $cmdData = [
            'id' => '9',
            'replacementIrhpPermit' => '1001'
        ];

        $command = Replace::create($cmdData);

        /** @var IrhpPermit $oldPermit */
        $oldPermit = m::mock(IrhpPermit::class)->makePartial();
        $oldPermit->setStatus(new RefData(IrhpPermit::STATUS_CEASED));

        $this->repoMap['IrhpPermit']
            ->shouldReceive('fetchById')
            ->with($cmdData['id'])
            ->andReturn($oldPermit);

        $oldPermit->shouldReceive('isNotIssued')
            ->andReturn(true);

        $this->sut->handleCommand($command);
    }

    /**
     * Tests exception thrown for wrong status
     *
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\NotFoundException
     */
    public function testBadRangeException()
    {
        $cmdData = [
            'id' => '9',
            'replacementIrhpPermit' => '1001'
        ];

        $command = Replace::create($cmdData);

        /** @var IrhpPermit $oldPermit */
        $oldPermit = m::mock(IrhpPermit::class)->makePartial();
        $oldPermit->setStatus(new RefData(IrhpPermit::STATUS_CEASED));

        $oldRange = m::mock(IrhpPermitRange::class)->makePartial();
        /** @var IrhpPermitStock $oldStock */
        $oldStock = m::mock(IrhpPermitStock::class)->makePartial();
        $oldStock->setValidTo(new DateTime());
        $oldRange->setIrhpPermitStock($oldStock);
        $oldPermit->setIrhpPermitRange($oldRange);
        $oldPermit->setId($cmdData['id']);

        $this->repoMap['IrhpPermit']
            ->shouldReceive('fetchById')
            ->with($cmdData['id'])
            ->andReturn($oldPermit);

        $oldPermit->shouldReceive('isNotIssued')
            ->andReturn(false);

        $this->sut->shouldReceive('handleQuery')
            ->once()
            ->andReturnUsing(function ($query) {
                return [
                ];
            });

        $this->sut->handleCommand($command);
    }
}
