<?php

/**
 * Update Vehicles Status Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\ApplicationCompletion;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\ApplicationCompletion\UpdateVehiclesStatus as Cmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\ApplicationCompletion\UpdateVehiclesStatus;
use Mockery as m;
use Dvsa\Olcs\Api\Entity\Application\ApplicationCompletion as ApplicationCompletionEntity;

/**
 * Update Vehicles Status Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class UpdateVehiclesStatusTest extends AbstractUpdateStatusTestCase
{
    protected $section = 'Vehicles';

    public function setUp()
    {
        $this->sut = new UpdateVehiclesStatus();
        $this->command = Cmd::create(['id' => 111]);

        parent::setUp();
    }

    public function testHandleCommandWithChange()
    {
        $this->applicationCompletion->setVehiclesStatus(ApplicationCompletionEntity::STATUS_NOT_STARTED);

        $this->application->setHasEnteredReg('N');

        $this->expectStatusChange(ApplicationCompletionEntity::STATUS_COMPLETE);
    }

    public function testHandleCommandWithoutChange()
    {
        $this->applicationCompletion->setVehiclesStatus(ApplicationCompletionEntity::STATUS_COMPLETE);

        $this->application->setHasEnteredReg('N');

        $this->expectStatusUnchanged(ApplicationCompletionEntity::STATUS_COMPLETE);
    }

    public function testHandleCommandWithoutVehicles()
    {
        $this->applicationCompletion->setVehiclesStatus(ApplicationCompletionEntity::STATUS_NOT_STARTED);

        $this->application->setHasEnteredReg('Y');

        $this->licence->shouldReceive('getActiveVehicles')->with(false)->once()->andReturn(
            new \Doctrine\Common\Collections\ArrayCollection()
        );

        $this->expectStatusChange(ApplicationCompletionEntity::STATUS_INCOMPLETE);
    }

    public function testHandleCommandWithMoreThanTotAuth()
    {
        $this->applicationCompletion->setVehiclesStatus(ApplicationCompletionEntity::STATUS_NOT_STARTED);

        $this->application->setHasEnteredReg('Y');
        $this->application->setTotAuthVehicles(0);

        $this->licence->shouldReceive('getActiveVehicles')->with(false)->once()->andReturn(
            new \Doctrine\Common\Collections\ArrayCollection(['foo'])
        );

        $this->expectStatusChange(ApplicationCompletionEntity::STATUS_INCOMPLETE);
    }

    public function testHandleCommand()
    {
        $this->applicationCompletion->setVehiclesStatus(ApplicationCompletionEntity::STATUS_NOT_STARTED);

        $this->application->setHasEnteredReg('Y');
        $this->application->setTotAuthVehicles(10);

        $this->licence->shouldReceive('getActiveVehicles')->with(false)->once()->andReturn(
            new \Doctrine\Common\Collections\ArrayCollection(['foo'])
        );

        $this->expectStatusChange(ApplicationCompletionEntity::STATUS_COMPLETE);
    }
}
