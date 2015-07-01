<?php

/**
 * Update Vehicles Declarations Status Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\ApplicationCompletion;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\ApplicationCompletion\UpdateVehiclesDeclarationsStatus as Cmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\ApplicationCompletion\UpdateVehiclesDeclarationsStatus;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea;
use Mockery as m;
use Dvsa\Olcs\Api\Entity\Application\ApplicationCompletion as ApplicationCompletionEntity;

/**
 * Update Vehicles Declarations Status Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class UpdateVehiclesDeclarationsStatusTest extends AbstractUpdateStatusTestCase
{
    protected $section = 'VehiclesDeclarations';

    public function setUp()
    {
        $this->sut = new UpdateVehiclesDeclarationsStatus();
        $this->command = Cmd::create(['id' => 111]);

        parent::setUp();
    }

    public function initReferences()
    {
        $this->refData = [
            Licence::LICENCE_TYPE_STANDARD_NATIONAL,
            Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL,
            Licence::LICENCE_TYPE_RESTRICTED,
            Licence::LICENCE_CATEGORY_GOODS_VEHICLE,
            Licence::LICENCE_CATEGORY_PSV
        ];

        $this->references = [
            TrafficArea::class => [
                TrafficArea::SCOTTISH_TRAFFIC_AREA_CODE => m::mock(TrafficArea::class)->makePartial()
                    ->shouldReceive('getIsScotland')->andReturn(true)->getMock()
            ]
        ];

        parent::initReferences();
    }

    public function testHandleCommandWithChange()
    {
        $this->applicationCompletion->setVehiclesDeclarationsStatus(ApplicationCompletionEntity::STATUS_NOT_STARTED);

        $this->application->setLicenceType($this->refData[Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL]);

        $this->expectStatusChange(ApplicationCompletionEntity::STATUS_INCOMPLETE);
    }

    public function testHandleCommandWithoutChange()
    {
        $this->applicationCompletion->setVehiclesDeclarationsStatus(ApplicationCompletionEntity::STATUS_INCOMPLETE);

        $this->application->setLicenceType($this->refData[Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL]);

        $this->expectStatusUnchanged(ApplicationCompletionEntity::STATUS_INCOMPLETE);
    }

    public function testHandleCommandWithoutLimo()
    {
        $this->applicationCompletion->setVehiclesDeclarationsStatus(ApplicationCompletionEntity::STATUS_NOT_STARTED);

        $this->application->setLicenceType($this->refData[Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL]);
        $this->application->setTotAuthSmallVehicles(3);
        $this->application->setTotAuthMediumVehicles(3);
        $this->application->setTotAuthLargeVehicles(3);

        $this->expectStatusChange(ApplicationCompletionEntity::STATUS_INCOMPLETE);
    }

    public function testHandleCommandWithoutNoLimo()
    {
        $this->applicationCompletion->setVehiclesDeclarationsStatus(ApplicationCompletionEntity::STATUS_NOT_STARTED);

        $this->application->setLicenceType($this->refData[Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL]);
        $this->application->setTotAuthSmallVehicles(3);
        $this->application->setTotAuthMediumVehicles(3);
        $this->application->setTotAuthLargeVehicles(3);

        $this->application->setPsvLimousines('Y');

        $this->expectStatusChange(ApplicationCompletionEntity::STATUS_INCOMPLETE);
    }

    public function testHandleCommandWithoutNoSmallVehConf()
    {
        $this->applicationCompletion->setVehiclesDeclarationsStatus(ApplicationCompletionEntity::STATUS_NOT_STARTED);

        $this->application->setLicenceType($this->refData[Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL]);
        $this->application->setTotAuthSmallVehicles(0);
        $this->application->setTotAuthMediumVehicles(3);
        $this->application->setTotAuthLargeVehicles(3);

        $this->application->setPsvLimousines('Y');
        $this->application->setPsvNoLimousineConfirmation('Y');

        $this->expectStatusChange(ApplicationCompletionEntity::STATUS_INCOMPLETE);
    }

    public function testHandleCommandWithoutPsvOnlyLimoConf()
    {
        $this->applicationCompletion->setVehiclesDeclarationsStatus(ApplicationCompletionEntity::STATUS_NOT_STARTED);

        $this->application->setLicenceType($this->refData[Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL]);
        $this->application->setTotAuthSmallVehicles(0);
        $this->application->setTotAuthMediumVehicles(3);
        $this->application->setTotAuthLargeVehicles(3);

        $this->application->setPsvLimousines('Y');
        $this->application->setPsvNoLimousineConfirmation('Y');
        $this->application->setPsvNoSmallVhlConfirmation('Y');

        $this->expectStatusChange(ApplicationCompletionEntity::STATUS_INCOMPLETE);
    }

    public function testHandleCommandNoSmallVh()
    {
        $this->applicationCompletion->setVehiclesDeclarationsStatus(ApplicationCompletionEntity::STATUS_NOT_STARTED);

        $this->application->setLicenceType($this->refData[Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL]);
        $this->application->setTotAuthSmallVehicles(0);
        $this->application->setTotAuthMediumVehicles(3);
        $this->application->setTotAuthLargeVehicles(3);

        $this->application->setPsvLimousines('Y');
        $this->application->setPsvNoLimousineConfirmation('Y');
        $this->application->setPsvNoSmallVhlConfirmation('Y');
        $this->application->setPsvOnlyLimousinesConfirmation('Y');

        $this->expectStatusChange(ApplicationCompletionEntity::STATUS_COMPLETE);
    }

    public function testHandleCommandSmallVhNoSmallVhlConf()
    {
        $this->applicationCompletion->setVehiclesDeclarationsStatus(ApplicationCompletionEntity::STATUS_NOT_STARTED);

        $this->application->setLicenceType($this->refData[Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL]);
        $this->application->setTotAuthSmallVehicles(3);
        $this->application->setTotAuthMediumVehicles(3);
        $this->application->setTotAuthLargeVehicles(3);

        $this->application->setPsvLimousines('Y');
        $this->application->setPsvNoLimousineConfirmation('Y');

        $this->expectStatusChange(ApplicationCompletionEntity::STATUS_INCOMPLETE);
    }

    public function testHandleCommandSmallVhNoSmallVhlNotes()
    {
        $this->applicationCompletion->setVehiclesDeclarationsStatus(ApplicationCompletionEntity::STATUS_NOT_STARTED);

        $this->application->setLicenceType($this->refData[Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL]);
        $this->application->setTotAuthSmallVehicles(3);
        $this->application->setTotAuthMediumVehicles(3);
        $this->application->setTotAuthLargeVehicles(3);

        $this->application->setPsvLimousines('Y');
        $this->application->setPsvNoLimousineConfirmation('Y');
        $this->application->setPsvSmallVhlConfirmation('Y');
        $this->application->setPsvOperateSmallVhl('Y');

        $this->expectStatusChange(ApplicationCompletionEntity::STATUS_INCOMPLETE);
    }

    public function testHandleCommandNoMedVhlNoLimoConf()
    {
        $this->applicationCompletion->setVehiclesDeclarationsStatus(ApplicationCompletionEntity::STATUS_NOT_STARTED);

        $this->application->setLicenceType($this->refData[Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL]);
        $this->application->setTotAuthSmallVehicles(3);
        $this->application->setTotAuthMediumVehicles(0);
        $this->application->setTotAuthLargeVehicles(3);

        $this->application->setPsvLimousines('Y');
        $this->application->setPsvNoLimousineConfirmation('Y');
        $this->application->setPsvSmallVhlConfirmation('Y');
        $this->application->setPsvSmallVhlNotes('Some notes');

        $this->expectStatusChange(ApplicationCompletionEntity::STATUS_INCOMPLETE);
    }

    public function testHandleCommandNotScotlandNoSmallVhl()
    {
        $this->applicationCompletion->setVehiclesDeclarationsStatus(ApplicationCompletionEntity::STATUS_NOT_STARTED);

        $this->application->setLicenceType($this->refData[Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL]);
        $this->application->setTotAuthSmallVehicles(3);
        $this->application->setTotAuthMediumVehicles(0);
        $this->application->setTotAuthLargeVehicles(3);

        $this->application->setPsvLimousines('Y');
        $this->application->setPsvNoLimousineConfirmation('Y');
        $this->application->setPsvSmallVhlConfirmation('Y');
        $this->application->setPsvSmallVhlNotes('Some notes');

        $this->application->setPsvOnlyLimousinesConfirmation('Y');

        $this->expectStatusChange(ApplicationCompletionEntity::STATUS_INCOMPLETE);
    }

    public function testHandleCommandNotScotlandNoSmallVhlNotes()
    {
        $this->applicationCompletion->setVehiclesDeclarationsStatus(ApplicationCompletionEntity::STATUS_NOT_STARTED);

        $this->application->setLicenceType($this->refData[Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL]);
        $this->application->setTotAuthSmallVehicles(0);
        $this->application->setTotAuthMediumVehicles(0);
        $this->application->setTotAuthLargeVehicles(3);

        $this->application->setPsvLimousines('Y');
        $this->application->setPsvNoLimousineConfirmation('Y');
        $this->application->setPsvSmallVhlConfirmation('Y');

        $this->application->setPsvOnlyLimousinesConfirmation('Y');

        $this->application->setPsvOperateSmallVhl('N');

        $this->expectStatusChange(ApplicationCompletionEntity::STATUS_INCOMPLETE);
    }

    public function testHandleCommandNotScotland()
    {
        $this->applicationCompletion->setVehiclesDeclarationsStatus(ApplicationCompletionEntity::STATUS_NOT_STARTED);

        $this->application->setLicenceType($this->refData[Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL]);
        $this->application->setTotAuthSmallVehicles(3);
        $this->application->setTotAuthMediumVehicles(0);
        $this->application->setTotAuthLargeVehicles(3);

        $this->application->setPsvLimousines('Y');
        $this->application->setPsvNoLimousineConfirmation('Y');
        $this->application->setPsvSmallVhlConfirmation('Y');
        $this->application->setPsvSmallVhlNotes('Some notes');

        $this->application->setPsvOnlyLimousinesConfirmation('Y');

        $this->application->setPsvOperateSmallVhl('N');
        $this->application->setPsvSmallVhlNotes('Foo');

        $this->expectStatusChange(ApplicationCompletionEntity::STATUS_COMPLETE);
    }

    public function testHandleCommandScotland()
    {
        $this->applicationCompletion->setVehiclesDeclarationsStatus(ApplicationCompletionEntity::STATUS_NOT_STARTED);

        $this->application->setLicenceType($this->refData[Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL]);
        $this->application->setTotAuthSmallVehicles(3);
        $this->application->setTotAuthMediumVehicles(0);
        $this->application->setTotAuthLargeVehicles(3);

        $this->application->setPsvLimousines('Y');
        $this->application->setPsvNoLimousineConfirmation('Y');
        $this->application->setPsvSmallVhlConfirmation('Y');

        $this->application->setPsvOnlyLimousinesConfirmation('Y');

        $this->licence->setTrafficArea($this->references[TrafficArea::class][TrafficArea::SCOTTISH_TRAFFIC_AREA_CODE]);

        $this->expectStatusChange(ApplicationCompletionEntity::STATUS_COMPLETE);
    }

    public function testHandleCommandSmall()
    {
        $this->applicationCompletion->setVehiclesDeclarationsStatus(ApplicationCompletionEntity::STATUS_NOT_STARTED);

        $this->application->setLicenceType($this->refData[Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL]);
        $this->application->setTotAuthSmallVehicles(3);
        $this->application->setTotAuthMediumVehicles(0);
        $this->application->setTotAuthLargeVehicles(3);

        $this->application->setPsvLimousines('Y');
        $this->application->setPsvNoLimousineConfirmation('Y');
        $this->application->setPsvSmallVhlConfirmation('Y');

        $this->application->setPsvOnlyLimousinesConfirmation('Y');

        $this->licence->setTrafficArea($this->references[TrafficArea::class][TrafficArea::SCOTTISH_TRAFFIC_AREA_CODE]);

        $this->expectStatusChange(ApplicationCompletionEntity::STATUS_COMPLETE);
    }

    public function testHandleCommandRestricted()
    {
        $this->applicationCompletion->setVehiclesDeclarationsStatus(ApplicationCompletionEntity::STATUS_NOT_STARTED);

        $this->application->setLicenceType($this->refData[Licence::LICENCE_TYPE_RESTRICTED]);
        $this->application->setTotAuthSmallVehicles(3);
        $this->application->setTotAuthMediumVehicles(3);
        $this->application->setTotAuthLargeVehicles(3);

        $this->application->setPsvMediumVhlConfirmation('Y');
        $this->application->setPsvMediumVhlNotes('foobar');

        $this->application->setPsvLimousines('Y');
        $this->application->setPsvNoLimousineConfirmation('Y');
        $this->application->setPsvSmallVhlConfirmation('Y');

        $this->application->setPsvOnlyLimousinesConfirmation('Y');

        $this->licence->setTrafficArea($this->references[TrafficArea::class][TrafficArea::SCOTTISH_TRAFFIC_AREA_CODE]);

        $this->expectStatusChange(ApplicationCompletionEntity::STATUS_COMPLETE);
    }

    public function testHandleCommandRestrictedFail1()
    {
        $this->applicationCompletion->setVehiclesDeclarationsStatus(ApplicationCompletionEntity::STATUS_NOT_STARTED);

        $this->application->setLicenceType($this->refData[Licence::LICENCE_TYPE_RESTRICTED]);
        $this->application->setTotAuthSmallVehicles(3);
        $this->application->setTotAuthMediumVehicles(3);
        $this->application->setTotAuthLargeVehicles(3);

        $this->application->setPsvMediumVhlNotes('foobar');

        $this->application->setPsvLimousines('Y');
        $this->application->setPsvNoLimousineConfirmation('Y');
        $this->application->setPsvSmallVhlConfirmation('Y');

        $this->application->setPsvOnlyLimousinesConfirmation('Y');

        $this->licence->setTrafficArea($this->references[TrafficArea::class][TrafficArea::SCOTTISH_TRAFFIC_AREA_CODE]);

        $this->expectStatusChange(ApplicationCompletionEntity::STATUS_INCOMPLETE);
    }

    public function testHandleCommandRestrictedFail2()
    {
        $this->applicationCompletion->setVehiclesDeclarationsStatus(ApplicationCompletionEntity::STATUS_NOT_STARTED);

        $this->application->setLicenceType($this->refData[Licence::LICENCE_TYPE_RESTRICTED]);
        $this->application->setTotAuthSmallVehicles(3);
        $this->application->setTotAuthMediumVehicles(3);
        $this->application->setTotAuthLargeVehicles(3);

        $this->application->setPsvMediumVhlConfirmation('Y');

        $this->application->setPsvLimousines('Y');
        $this->application->setPsvNoLimousineConfirmation('Y');
        $this->application->setPsvSmallVhlConfirmation('Y');

        $this->application->setPsvOnlyLimousinesConfirmation('Y');

        $this->licence->setTrafficArea($this->references[TrafficArea::class][TrafficArea::SCOTTISH_TRAFFIC_AREA_CODE]);

        $this->expectStatusChange(ApplicationCompletionEntity::STATUS_INCOMPLETE);
    }

    public function testHandleCommandRestrictedNoLimoConf()
    {
        $this->applicationCompletion->setVehiclesDeclarationsStatus(ApplicationCompletionEntity::STATUS_NOT_STARTED);

        $this->application->setLicenceType($this->refData[Licence::LICENCE_TYPE_STANDARD_NATIONAL]);
        $this->application->setTotAuthSmallVehicles(0);
        $this->application->setTotAuthMediumVehicles(0);
        $this->application->setTotAuthLargeVehicles(0);

        $this->application->setPsvLimousines('N');
        $this->application->setPsvSmallVhlConfirmation('Y');

        $this->expectStatusChange(ApplicationCompletionEntity::STATUS_INCOMPLETE);
    }

    public function testHandleCommandLimoConf()
    {
        $this->applicationCompletion->setVehiclesDeclarationsStatus(ApplicationCompletionEntity::STATUS_NOT_STARTED);

        $this->application->setLicenceType($this->refData[Licence::LICENCE_TYPE_STANDARD_NATIONAL]);
        $this->application->setTotAuthSmallVehicles(2);
        $this->application->setTotAuthMediumVehicles(0);
        $this->application->setTotAuthLargeVehicles(0);

        $this->application->setPsvOperateSmallVhl('Y');
        $this->application->setPsvSmallVhlNotes('foobar');

        $this->application->setPsvLimousines('N');
        $this->application->setPsvNoLimousineConfirmation('Y');

        $this->expectStatusChange(ApplicationCompletionEntity::STATUS_COMPLETE);
    }

    public function testHandleCommandSmallNo()
    {
        $this->applicationCompletion->setVehiclesDeclarationsStatus(ApplicationCompletionEntity::STATUS_NOT_STARTED);

        $this->application->setLicenceType($this->refData[Licence::LICENCE_TYPE_STANDARD_NATIONAL]);
        $this->application->setTotAuthSmallVehicles(3);

        $this->application->setPsvOperateSmallVhl('N');

        $this->expectStatusChange(ApplicationCompletionEntity::STATUS_INCOMPLETE);
    }

    public function testHandleCommandSmallNoScotland()
    {
        $this->applicationCompletion->setVehiclesDeclarationsStatus(ApplicationCompletionEntity::STATUS_NOT_STARTED);

        $this->application->setLicenceType($this->refData[Licence::LICENCE_TYPE_STANDARD_NATIONAL]);
        $this->application->setTotAuthSmallVehicles(3);
        $this->application->setTotAuthMediumVehicles(0);
        $this->application->setTotAuthLargeVehicles(0);

        $this->licence->setTrafficArea($this->references[TrafficArea::class][TrafficArea::SCOTTISH_TRAFFIC_AREA_CODE]);

        $this->expectStatusChange(ApplicationCompletionEntity::STATUS_INCOMPLETE);
    }
}
