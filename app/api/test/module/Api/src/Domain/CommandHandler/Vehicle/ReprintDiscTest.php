<?php

/**
 * Reprint Disc Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Vehicle;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Command\Vehicle\CeaseActiveDiscs;
use Dvsa\Olcs\Api\Domain\Command\Vehicle\CreateGoodsDiscs;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\Vehicle\ReprintDisc;
use Dvsa\Olcs\Api\Domain\Repository\GoodsDisc as GoodsDiscRepo;
use Dvsa\Olcs\Transfer\Command\Vehicle\ReprintDisc as Cmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;

/**
 * Reprint Disc Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ReprintDiscTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new ReprintDisc();
        $this->mockRepo('GoodsDisc', GoodsDiscRepo::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [];

        $this->references = [];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $data = [
            'ids' => [
                111, 222
            ]
        ];
        $command = Cmd::create($data);

        $result1 = new Result();
        $result1->addMessage('2 Disc(s) Ceased');
        $this->expectedSideEffect(CeaseActiveDiscs::class, $data, $result1);

        $result2 = new Result();
        $result2->addMessage('2 Disc(s) Created');
        $data['isCopy'] = 'Y';
        $this->expectedSideEffect(CreateGoodsDiscs::class, $data, $result2);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                '2 Disc(s) Ceased',
                '2 Disc(s) Created'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
