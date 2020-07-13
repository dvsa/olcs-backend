<?php

/**
 * Update Operating Centres Status Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\ApplicationCompletion;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\ApplicationCompletion\UpdateOperatingCentresStatus as Cmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\ApplicationCompletion\UpdateOperatingCentresStatus;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Mockery as m;
use Dvsa\Olcs\Api\Entity\Application\ApplicationCompletion as ApplicationCompletionEntity;

/**
 * Update Operating Centres Status Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class UpdateOperatingCentresStatusTest extends AbstractUpdateStatusTestCase
{
    protected $section = 'OperatingCentres';

    public function setUp(): void
    {
        $this->sut = new UpdateOperatingCentresStatus();
        $this->command = Cmd::create(['id' => 111]);

        parent::setUp();
    }

    public function initReferences()
    {
        $this->refData = [
            Licence::LICENCE_CATEGORY_GOODS_VEHICLE,
            Licence::LICENCE_CATEGORY_PSV,
            Licence::LICENCE_TYPE_STANDARD_NATIONAL,
            Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL,
            Licence::LICENCE_TYPE_RESTRICTED
        ];

        parent::initReferences();
    }

    public function testHandleCommandWithChange()
    {
        $this->applicationCompletion->setOperatingCentresStatus(ApplicationCompletionEntity::STATUS_NOT_STARTED);

        $this->application->setOperatingCentres([]);

        $this->expectStatusChange(ApplicationCompletionEntity::STATUS_INCOMPLETE);
    }

    public function testHandleCommandWithoutChange()
    {
        $this->applicationCompletion->setOperatingCentresStatus(ApplicationCompletionEntity::STATUS_INCOMPLETE);

        $this->application->setOperatingCentres([]);

        $this->expectStatusUnchanged(ApplicationCompletionEntity::STATUS_INCOMPLETE);
    }

    public function testHandleCommandGvNoTotAuth()
    {
        $this->applicationCompletion->setOperatingCentresStatus(ApplicationCompletionEntity::STATUS_NOT_STARTED);

        $this->application->setGoodsOrPsv($this->refData[Licence::LICENCE_CATEGORY_GOODS_VEHICLE]);

        $this->application->setOperatingCentres(['foo']);

        $this->expectStatusChange(ApplicationCompletionEntity::STATUS_INCOMPLETE);
    }

    public function testHandleCommandGvNoTotAuthTrailers()
    {
        $this->applicationCompletion->setOperatingCentresStatus(ApplicationCompletionEntity::STATUS_NOT_STARTED);

        $this->application->setGoodsOrPsv($this->refData[Licence::LICENCE_CATEGORY_GOODS_VEHICLE]);

        $this->application->setOperatingCentres(['foo']);
        $this->application->setTotAuthVehicles(10);

        $this->expectStatusChange(ApplicationCompletionEntity::STATUS_INCOMPLETE);
    }

    public function testHandleCommandGv()
    {
        $this->applicationCompletion->setOperatingCentresStatus(ApplicationCompletionEntity::STATUS_NOT_STARTED);

        $this->application->setGoodsOrPsv($this->refData[Licence::LICENCE_CATEGORY_GOODS_VEHICLE]);

        $this->application->setOperatingCentres(['foo']);
        $this->application->setTotAuthVehicles(10);
        $this->application->setTotAuthTrailers(10);

        $this->expectStatusChange(ApplicationCompletionEntity::STATUS_COMPLETE);
    }
}
