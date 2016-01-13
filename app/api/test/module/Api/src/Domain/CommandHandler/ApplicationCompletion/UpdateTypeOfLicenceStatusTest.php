<?php

/**
 * Update Type Of Licence Status Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\ApplicationCompletion;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\ApplicationCompletion\UpdateTypeOfLicenceStatus as Cmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\ApplicationCompletion\UpdateTypeOfLicenceStatus;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Mockery as m;
use Dvsa\Olcs\Api\Entity\Application\ApplicationCompletion as ApplicationCompletionEntity;

/**
 * Update Type Of Licence Status Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class UpdateTypeOfLicenceStatusTest extends AbstractUpdateStatusTestCase
{
    protected $section = 'TypeOfLicence';

    public function setUp()
    {
        $this->sut = new UpdateTypeOfLicenceStatus();
        $this->command = Cmd::create(['id' => 111]);

        parent::setUp();
    }

    public function initReferences()
    {
        $this->refData = [
            Licence::LICENCE_TYPE_STANDARD_NATIONAL,
            Licence::LICENCE_TYPE_RESTRICTED,
            Licence::LICENCE_CATEGORY_GOODS_VEHICLE,
            Licence::LICENCE_CATEGORY_PSV
        ];

        parent::initReferences();
    }

    public function testHandleCommandWithChange()
    {
        $this->applicationCompletion->setTypeOfLicenceStatus(ApplicationCompletionEntity::STATUS_NOT_STARTED);

        $this->expectStatusChange(ApplicationCompletionEntity::STATUS_INCOMPLETE);
    }

    public function testHandleCommandWithoutChange()
    {
        $this->applicationCompletion->setTypeOfLicenceStatus(ApplicationCompletionEntity::STATUS_INCOMPLETE);

        $this->expectStatusUnchanged(ApplicationCompletionEntity::STATUS_INCOMPLETE);
    }

    public function testHandleCommandWithoutGoodsOrPsv()
    {
        $this->applicationCompletion->setTypeOfLicenceStatus(ApplicationCompletionEntity::STATUS_NOT_STARTED);

        $this->application->setNiFlag('Y');

        $this->expectStatusChange(ApplicationCompletionEntity::STATUS_INCOMPLETE);
    }

    public function testHandleCommandWithoutLicenceType()
    {
        $this->applicationCompletion->setTypeOfLicenceStatus(ApplicationCompletionEntity::STATUS_NOT_STARTED);

        $this->application->setNiFlag('Y');
        $this->application->setGoodsOrPsv($this->refData[Licence::LICENCE_CATEGORY_GOODS_VEHICLE]);

        $this->expectStatusChange(ApplicationCompletionEntity::STATUS_INCOMPLETE);
    }

    public function testHandleCommandWithInvalidCombo()
    {
        $this->applicationCompletion->setTypeOfLicenceStatus(ApplicationCompletionEntity::STATUS_NOT_STARTED);

        $this->application->setNiFlag('Y');
        $this->application->setGoodsOrPsv($this->refData[Licence::LICENCE_CATEGORY_PSV]);
        $this->application->setLicenceType($this->refData[Licence::LICENCE_TYPE_STANDARD_NATIONAL]);

        $this->expectStatusChange(ApplicationCompletionEntity::STATUS_INCOMPLETE);
    }

    public function testHandleCommand()
    {
        $this->applicationCompletion->setTypeOfLicenceStatus(ApplicationCompletionEntity::STATUS_NOT_STARTED);

        $this->application->setNiFlag('N');
        $this->application->setGoodsOrPsv($this->refData[Licence::LICENCE_CATEGORY_PSV]);
        $this->application->setLicenceType($this->refData[Licence::LICENCE_TYPE_STANDARD_NATIONAL]);

        $this->expectStatusChange(ApplicationCompletionEntity::STATUS_COMPLETE);
    }
}
