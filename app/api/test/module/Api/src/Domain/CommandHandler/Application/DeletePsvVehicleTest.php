<?php

/**
 * Delete Psv Vehicle Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Application;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\Command\Application\UpdateApplicationCompletion;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Application\DeletePsvVehicle;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\AbstractCommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Application as ApplicationRepo;
use Dvsa\Olcs\Transfer\Command\Application\DeletePsvVehicle as Cmd;
use Dvsa\Olcs\Transfer\Command\Vehicle\DeleteLicenceVehicle as VehicleCmd;

/**
 * Delete Psv Vehicle Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class DeletePsvVehicleTest extends AbstractCommandHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new DeletePsvVehicle();
        $this->mockRepo('Application', ApplicationRepo::class);

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
            'application' => 111,
            'ids' => [123, 456]
        ];
        $command = Cmd::create($data);

        $data = [
            'ids' => [123, 456]
        ];
        $result1 = new Result();
        $result1->addMessage('Psv Vehicle Deleted');
        $this->expectedSideEffect(VehicleCmd::class, $data, $result1);

        $data = [
            'id' => 111,
            'section' => 'vehiclesPsv'
        ];
        $result2 = new Result();
        $result2->addMessage('Section Updated');
        $this->expectedSideEffect(UpdateApplicationCompletion::class, $data, $result2);

        $result = $this->sut->handleCommand($command);

        $this->assertInstanceOf(Result::class, $result);

        $expected = [
            'id' => [],
            'messages' => [
                'Psv Vehicle Deleted',
                'Section Updated'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
