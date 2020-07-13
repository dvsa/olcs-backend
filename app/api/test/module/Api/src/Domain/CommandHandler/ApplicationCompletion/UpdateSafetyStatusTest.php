<?php

/**
 * Update Safety Status Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\ApplicationCompletion;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\ApplicationCompletion\UpdateSafetyStatus as Cmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\ApplicationCompletion\UpdateSafetyStatus;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Mockery as m;
use Dvsa\Olcs\Api\Entity\Application\ApplicationCompletion as ApplicationCompletionEntity;

/**
 * Update Safety Status Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class UpdateSafetyStatusTest extends AbstractUpdateStatusTestCase
{
    protected $section = 'Safety';

    public function setUp(): void
    {
        $this->sut = new UpdateSafetyStatus();
        $this->command = Cmd::create(['id' => 111]);

        parent::setUp();
    }

    public function initReferences()
    {
        $this->refData = [
            Licence::LICENCE_CATEGORY_GOODS_VEHICLE,
            Licence::LICENCE_CATEGORY_PSV,
            Licence::TACH_EXT
        ];

        parent::initReferences();
    }

    public function testHandleCommandWithChange()
    {
        $this->applicationCompletion->setSafetyStatus(ApplicationCompletionEntity::STATUS_NOT_STARTED);

        $this->expectStatusChange(ApplicationCompletionEntity::STATUS_INCOMPLETE);
    }

    public function testHandleCommandWithoutChange()
    {
        $this->applicationCompletion->setSafetyStatus(ApplicationCompletionEntity::STATUS_INCOMPLETE);

        $this->expectStatusUnchanged(ApplicationCompletionEntity::STATUS_INCOMPLETE);
    }

    public function testHandleCommandWithoutInsVaries()
    {
        $this->applicationCompletion->setSafetyStatus(ApplicationCompletionEntity::STATUS_NOT_STARTED);

        $this->licence->setSafetyInsVehicles(1);

        $this->expectStatusChange(ApplicationCompletionEntity::STATUS_INCOMPLETE);
    }

    public function testHandleCommandWithoutTachoIns()
    {
        $this->applicationCompletion->setSafetyStatus(ApplicationCompletionEntity::STATUS_NOT_STARTED);

        $this->licence->setSafetyInsVehicles(1);
        $this->licence->setSafetyInsVaries(1);

        $this->expectStatusChange(ApplicationCompletionEntity::STATUS_INCOMPLETE);
    }

    public function testHandleCommandWithoutWorkshops()
    {
        $this->applicationCompletion->setSafetyStatus(ApplicationCompletionEntity::STATUS_NOT_STARTED);

        $this->licence->setSafetyInsVehicles(1);
        $this->licence->setSafetyInsVaries(1);
        $this->licence->setTachographIns($this->refData[Licence::TACH_EXT]);

        $this->expectStatusChange(ApplicationCompletionEntity::STATUS_INCOMPLETE);
    }

    public function testHandleCommandWithoutConfirmation()
    {
        $this->applicationCompletion->setSafetyStatus(ApplicationCompletionEntity::STATUS_NOT_STARTED);

        $this->licence->setSafetyInsVehicles(1);
        $this->licence->setSafetyInsVaries(1);
        $this->licence->setTachographIns($this->refData[Licence::TACH_EXT]);
        $this->licence->setWorkshops(['foo']);
        $this->application->setSafetyConfirmation('N');

        $this->expectStatusChange(ApplicationCompletionEntity::STATUS_INCOMPLETE);
    }

    public function testHandleCommandWithoutTachoName()
    {
        $this->applicationCompletion->setSafetyStatus(ApplicationCompletionEntity::STATUS_NOT_STARTED);

        $this->licence->setSafetyInsVehicles(1);
        $this->licence->setSafetyInsVaries(1);
        $this->licence->setTachographIns($this->refData[Licence::TACH_EXT]);
        $this->licence->setWorkshops(['foo']);
        $this->application->setSafetyConfirmation('Y');

        $this->expectStatusChange(ApplicationCompletionEntity::STATUS_INCOMPLETE);
    }

    public function testHandleCommandWithoutTrailers()
    {
        $this->applicationCompletion->setSafetyStatus(ApplicationCompletionEntity::STATUS_NOT_STARTED);

        $this->application->setGoodsOrPsv($this->refData[Licence::LICENCE_CATEGORY_GOODS_VEHICLE]);

        $this->licence->setSafetyInsVehicles(1);
        $this->licence->setSafetyInsVaries('Y');
        $this->licence->setTachographIns($this->refData[Licence::TACH_EXT]);
        $this->licence->setWorkshops(['foo']);
        $this->application->setSafetyConfirmation('Y');
        $this->licence->setTachographInsName('Foo');

        $this->expectStatusChange(ApplicationCompletionEntity::STATUS_INCOMPLETE);
    }

    public function testHandleCommandWithTrailers()
    {
        $this->applicationCompletion->setSafetyStatus(ApplicationCompletionEntity::STATUS_NOT_STARTED);

        $this->application->setGoodsOrPsv($this->refData[Licence::LICENCE_CATEGORY_GOODS_VEHICLE]);

        $this->licence->setSafetyInsVehicles(1);
        $this->licence->setSafetyInsVaries('Y');
        $this->licence->setTachographIns($this->refData[Licence::TACH_EXT]);
        $this->licence->setWorkshops(['foo']);
        $this->application->setSafetyConfirmation('Y');
        $this->licence->setTachographInsName('Foo');
        $this->licence->setSafetyInsTrailers(1);

        $this->expectStatusChange(ApplicationCompletionEntity::STATUS_COMPLETE);
    }

    public function testHandleCommandPsv()
    {
        $this->applicationCompletion->setSafetyStatus(ApplicationCompletionEntity::STATUS_NOT_STARTED);

        $this->application->setGoodsOrPsv($this->refData[Licence::LICENCE_CATEGORY_PSV]);

        $this->licence->setSafetyInsVehicles(1);
        $this->licence->setSafetyInsVaries('Y');
        $this->licence->setTachographIns($this->refData[Licence::TACH_EXT]);
        $this->licence->setWorkshops(['foo']);
        $this->application->setSafetyConfirmation('Y');
        $this->licence->setTachographInsName('Foo');

        $this->expectStatusChange(ApplicationCompletionEntity::STATUS_COMPLETE);
    }
}
