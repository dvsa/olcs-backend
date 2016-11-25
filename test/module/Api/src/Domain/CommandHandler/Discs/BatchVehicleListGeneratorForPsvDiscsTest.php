<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Discs;

use Dvsa\Olcs\Api\Domain\Command\Discs\CreatePsvVehicleListForDiscs as CreatePsvVehicleListForDiscsCommand;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\CommandHandler\Discs\BatchVehicleListGeneratorForPsvDiscs as Batch;
use Dvsa\Olcs\Api\Domain\Command\Discs\BatchVehicleListGeneratorForPsvDiscs as Cmd;
use Dvsa\Olcs\Api\Domain\Command\Queue\Create as CreatQueue;
use Dvsa\Olcs\Api\Entity\Queue\Queue;

/**
 * BatchVehicleListGeneratorForPsvDiscs test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class BatchVehicleListGeneratorForPsvDiscsTest extends CommandHandlerTestCase
{
    protected $batchSize = 180;

    public function setUp()
    {
        $this->sut = new Batch();

        $this->mockedSmServices = [
            'Config' => [
                'disc_printing' => ['psv_vehicle_list_batch_size' => $this->batchSize]
            ]
        ];

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $queries = $this->getQueries();
        $bookmarks = $this->getBookmarks();
        $data = [
            'queries' => $queries,
            'bookmarks' => $bookmarks,
            'user' => 1
        ];
        $queuedQueries = array_slice($queries, $this->batchSize);
        $options = [
            'queries' => $queuedQueries,
            'bookmarks' => $bookmarks,
            'user' => 1
        ];

        $command = Cmd::create($data);
        $expected = ['id' => []];

        for ($i = 0; $i < $this->batchSize; $i++) {
            $data = [
                'id' =>  $i + 1,
                'knownValues' => ['foo' => 'bar' . $i],
                'user' => 1
            ];
            $this->expectedSideEffect(CreatePsvVehicleListForDiscsCommand::class, $data, new Result());
            $expected['messages'][] = 'Vehicle list generated for licence ' . $i;
        }

        $data = [
            'type' => Queue::TYPE_CREATE_PSV_VEHICLE_LIST,
            'status' => Queue::STATUS_QUEUED,
            'options' => json_encode($options)
        ];
        $this->expectedSideEffect(CreatQueue::class, $data, new Result());

        $result = $this->sut->handleCommand($command);
        $this->assertEquals($expected, $result->toArray());
    }

    protected function getBookmarks()
    {
        $params = [];
        for ($i = 0; $i < $this->batchSize + 2; $i++) {
            $params[$i] = ['foo' => 'bar' . $i];
        }
        return $params;
    }

    protected function getQueries()
    {
        $queries = [];
        for ($i = 0; $i <= $this->batchSize +  2; $i++) {
            $queries[$i + 1] = ['id' => $i + 1];
        }
        return $queries;
    }
}
