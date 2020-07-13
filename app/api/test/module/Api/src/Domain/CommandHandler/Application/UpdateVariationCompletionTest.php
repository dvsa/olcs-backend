<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Application;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Transfer\Query\Licence\Licence;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Repository\Application;
use Dvsa\Olcs\Api\Domain\CommandHandler\Application\UpdateVariationCompletion;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Command\Application\UpdateVariationCompletion as Cmd;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Application\ApplicationCompletion as ApplicationCompletionEntity;
use ZfcRbac\Service\AuthorizationService;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Transfer\Command\Application\UpdateOperatingCentres as UpdateOperatingCentresCmd;
use Dvsa\Olcs\Api\Domain\Service\VariationOperatingCentreHelper;
use Dvsa\Olcs\Api\Domain\Service\UpdateOperatingCentreHelper;

/**
 * Update Variation Completion Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class UpdateVariationCompletionTest extends CommandHandlerTestCase
{
    /** @var  UpdateOperatingCentreHelper */
    protected $updateHelper;

    /** @var  VariationOperatingCentreHelper */
    protected $vocHelper;

    public function setUp(): void
    {
        $this->updateHelper = m::mock();
        $this->vocHelper = m::mock();

        $this->mockedSmServices = [
            AuthorizationService::class => m::mock(AuthorizationService::class),
            'UpdateOperatingCentreHelper' => $this->updateHelper,
            'VariationOperatingCentreHelper' => $this->vocHelper,
        ];

        $this->sut = new UpdateVariationCompletion();
        $this->mockRepo('Application', Application::class);
        $this->mockedSmServices[AuthorizationService::class]
            ->shouldReceive('isGranted')
            ->with(Permission::INTERNAL_USER, null)
            ->andReturn(false);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            LicenceEntity::LICENCE_CATEGORY_PSV,
            LicenceEntity::LICENCE_CATEGORY_GOODS_VEHICLE,
            LicenceEntity::LICENCE_TYPE_STANDARD_NATIONAL,
            LicenceEntity::LICENCE_TYPE_STANDARD_INTERNATIONAL,
            LicenceEntity::LICENCE_TYPE_RESTRICTED
        ];

        parent::initReferences();
    }

    /**
     * @dataProvider handleCommandProvider
     */
    public function testHandleCommand(
        $section,
        ApplicationEntity $application,
        LicenceEntity $licence,
        array $previousStatuses = [],
        array $expectedStatuses = [],
        $expectedDeclarationConfirmation = '',
        array $commandData = []
    ) {
        $data = [
            'id' => 111,
            'section' => $section,
            'data' => $commandData
        ];
        $command = Cmd::create($data);

        /** @var ApplicationCompletionEntity $ac */
        $ac = $this->getConfiguredCompletion($previousStatuses);

        $application->setLicence($licence);
        $application->setApplicationCompletion($ac);

        $totals = [
            'noOfOperatingCentres' => 0,
            'minVehicleAuth' => 0,
            'maxVehicleAuth' => 0,
            'minTrailerAuth' => 0,
            'maxTrailerAuth' => 0,
        ];

        if ($section === 'operatingCentres') {
            if ($application->isPsv()) {
                $this->updateHelper
                    ->shouldReceive('validatePsv')
                    ->with($application, m::type(UpdateOperatingCentresCmd::class))
                    ->getMock();

                $this->vocHelper
                    ->shouldReceive('getListDataForApplication')
                    ->with($application)
                    ->once()
                    ->andReturn([]);
            } else {
                $this->updateHelper
                    ->shouldReceive('validateTotalAuthTrailers')
                    ->with(m::type(UpdateOperatingCentresCmd::class), $totals)
                    ->getMock();

                $this->vocHelper
                    ->shouldReceive('getListDataForApplication')
                    ->with($application)
                    ->twice()
                    ->andReturn([]);
            }
            $this->updateHelper
                ->shouldReceive('validateTotalAuthVehicles')
                ->with($application, m::type(UpdateOperatingCentresCmd::class), $totals)
                ->once()
                ->shouldReceive('getMessages')
                ->andReturn(['foo'])
                ->once();
        }

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->once()
            ->with($command)
            ->andReturn($application)
            ->shouldReceive('save')
            ->once()
            ->with($application);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'Updated variation completion status'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
        $this->assertExpectedStatuses($expectedStatuses, $ac);
        if ($section !== 'declarationsInternal') {
            $this->assertEquals($expectedDeclarationConfirmation, $application->getDeclarationConfirmation());
        } else {
            $this->assertEquals($expectedDeclarationConfirmation, $application->getAuthSignature());
        }
    }

    private function assertExpectedStatuses(array $expectedStatuses, ApplicationCompletionEntity $ac)
    {
        foreach ($expectedStatuses as $property => $status) {
            $this->assertEquals($status, $ac->{'get' . $property . 'Status'}());
        }
    }

    private function getConfiguredCompletion(array $statuses = [])
    {
        /** @var ApplicationCompletionEntity $ac */
        $ac = m::mock(ApplicationCompletionEntity::class)->makePartial();

        foreach ($statuses as $property => $status) {
            $ac->{'set' . $property . 'Status'}($status);
        }

        return $ac;
    }

    /**
     * @return LicenceEntity
     */
    private function newLicence()
    {
        return m::mock(LicenceEntity::class)->makePartial();
    }

    /**
     * @return ApplicationEntity
     */
    private function newApplication()
    {
        /** @var ApplicationEntity $application */
        $application = m::mock(ApplicationEntity::class)->makePartial();
        $application->setIsVariation(true);

        return $application;
    }

    private function getApplicationState1()
    {
        $application = $this->newApplication();

        $application->setGoodsOrPsv($this->refData[LicenceEntity::LICENCE_CATEGORY_GOODS_VEHICLE]);
        $application->setLicenceType($this->refData[LicenceEntity::LICENCE_TYPE_STANDARD_NATIONAL]);
        $application->setDeclarationConfirmation('Y');

        $tmCollection = new ArrayCollection();
        $tmCollection->add(['foo' => 'bar']);
        $application->setTransportManagers($tmCollection);

        $vehicleCollection = new ArrayCollection();
        $vehicleCollection->add(['foo' => 'bar']);
        $application->setLicenceVehicles($vehicleCollection);

        $conditionsCollection = new ArrayCollection();
        $conditionsCollection->add(['foo' => 'bar']);
        $application->setConditionUndertakings($conditionsCollection);

        $ocCollection = new ArrayCollection();
        $ocCollection->add(['foo' => 'bar']);
        $application->setOperatingCentres($ocCollection);

        $application->setPsvOperateSmallVhl('Y');

        $application->setBankrupt('Y');

        $application->setConvictionsConfirmation('Y');

        $aop = new ArrayCollection();
        $aop->add('foo');
        $application->setApplicationOrganisationPersons($aop);

        $application->shouldReceive('getActiveVehicles->count')->andReturn(3);
        $application->setTotAuthVehicles(10);

        return $application;
    }

    private function getApplicationState2()
    {
        $application = $this->newApplication();

        $application->setGoodsOrPsv($this->refData[LicenceEntity::LICENCE_CATEGORY_GOODS_VEHICLE]);
        $application->setLicenceType($this->refData[LicenceEntity::LICENCE_TYPE_STANDARD_INTERNATIONAL]);
        $application->setDeclarationConfirmation('Y');

        $tmCollection = new ArrayCollection();
        $application->setTransportManagers($tmCollection);

        $vehicleCollection = new ArrayCollection();
        $application->setLicenceVehicles($vehicleCollection);

        $conditionsCollection = new ArrayCollection();
        $application->setConditionUndertakings($conditionsCollection);

        $ocCollection = new ArrayCollection();
        $application->setOperatingCentres($ocCollection);

        $application->setTotAuthVehicles(10);

        $application->setConvictionsConfirmation(0);
        $application->setPrevConviction('Y');

        return $application;
    }

    private function getApplicationState3()
    {
        $application = $this->newApplication();

        $application->setDeclarationConfirmation('N');

        $application->setConvictionsConfirmation(0);

        return $application;
    }

    private function getApplicationState4()
    {
        $application = $this->newApplication();

        $application->setGoodsOrPsv($this->refData[LicenceEntity::LICENCE_CATEGORY_PSV]);
        $application->setLicenceType($this->refData[LicenceEntity::LICENCE_TYPE_STANDARD_INTERNATIONAL]);
        $application->setDeclarationConfirmation('Y');

        $vehicleCollection = new ArrayCollection();
        $application->setLicenceVehicles($vehicleCollection);

        $ocCollection = new ArrayCollection();
        $application->setOperatingCentres($ocCollection);

        $application->setTotAuthVehicles(2);

        return $application;
    }
    private function getApplicationState5()
    {
        $application = $this->newApplication();

        $application->setAuthSignature(true);

        return $application;
    }

    private function getApplicationState6($action)
    {
        $application = $this->newApplication();

        $application->setGoodsOrPsv($this->refData[LicenceEntity::LICENCE_CATEGORY_GOODS_VEHICLE]);
        $application->setLicenceType($this->refData[LicenceEntity::LICENCE_TYPE_STANDARD_NATIONAL]);

        $aop1 = m::mock()
            ->shouldReceive('getAction')
            ->andReturn($action)
            ->getMock();

        $aop = new ArrayCollection();
        $aop->add($aop1);

        $application->setApplicationOrganisationPersons($aop);

        return $application;
    }

    private function getLicenceState1()
    {
        $licence = $this->newLicence();

        $licence->setLicenceType($this->refData[LicenceEntity::LICENCE_TYPE_STANDARD_NATIONAL]);

        $vehicleCollection = new ArrayCollection();
        $vehicleCollection->add(['removalDate' => null]);
        $licence->setLicenceVehicles($vehicleCollection);

        $licence->setTotAuthVehicles(5);

        $licence->shouldReceive('getPsvDiscsNotCeasedCount')->andReturn(6);
        $licence->shouldReceive('getActiveCommunityLicences->count')->andReturn(6);

        $licence->shouldReceive('getOrganisation->getType->getId')->andReturn('bar');

        return $licence;
    }

    private function getLicenceState2()
    {
        $licence = $this->newLicence();

        $licence->setLicenceType($this->refData[LicenceEntity::LICENCE_TYPE_STANDARD_INTERNATIONAL]);

        $vehicleCollection = new ArrayCollection();
        $licence->setLicenceVehicles($vehicleCollection);

        $licence->shouldReceive('getActiveCommunityLicences->count')->andReturn(6);

        $licence->setTotAuthVehicles(10);

        return $licence;
    }

    private function getLicenceState3()
    {
        $licence = $this->newLicence();

        $licence->setLicenceType($this->refData[LicenceEntity::LICENCE_TYPE_RESTRICTED]);

        $vehicleCollection = new ArrayCollection();
        $licence->setLicenceVehicles($vehicleCollection);

        $psvDiscsCollection = new ArrayCollection();
        $psvDiscsCollection->add(['foo' => 'bar', 'ceasedDate' => null]);
        $psvDiscsCollection->add(['foo' => 'bar', 'ceasedDate' => null]);
        $psvDiscsCollection->add(['foo' => 'bar', 'ceasedDate' => null]);
        $licence->setPsvDiscs($psvDiscsCollection);

        $licence->shouldReceive('getActiveCommunityLicences->count')->andReturn(6);

        return $licence;
    }

    private function getLicenceState4()
    {
        $licence = $this->newLicence();

        $licence->setLicenceType($this->refData[LicenceEntity::LICENCE_TYPE_RESTRICTED]);

        $vehicleCollection = new ArrayCollection();
        $licence->setLicenceVehicles($vehicleCollection);

        $psvDiscsCollection = new ArrayCollection();
        $psvDiscsCollection->add(['foo' => 'bar', 'ceasedDate' => null]);
        $licence->setPsvDiscs($psvDiscsCollection);

        $licence->setTotAuthVehicles(3);

        $licence->shouldReceive('getActiveCommunityLicences->count')->andReturn(6);

        return $licence;
    }

    public function handleCommandProvider()
    {
        $this->initReferences();

        return [
            'Changed Type Of Licence' => [
                'typeOfLicence',
                $this->getApplicationState1(),
                $this->getLicenceState2(),
                [
                    'TypeOfLicence' => UpdateVariationCompletion::STATUS_UNCHANGED,
                    'Undertakings' => UpdateVariationCompletion::STATUS_UPDATED
                ],
                [
                    'TypeOfLicence' => UpdateVariationCompletion::STATUS_UPDATED,
                    'Undertakings' => UpdateVariationCompletion::STATUS_REQUIRES_ATTENTION
                ],
                'N',
                []
            ],
            'Unchanged Type Of Licence' => [
                'typeOfLicence',
                $this->getApplicationState2(),
                $this->getLicenceState2(),
                [
                    'TypeOfLicence' => UpdateVariationCompletion::STATUS_UPDATED,
                    'Undertakings' => UpdateVariationCompletion::STATUS_UPDATED
                ],
                [
                    'TypeOfLicence' => UpdateVariationCompletion::STATUS_UNCHANGED,
                    'Undertakings' => UpdateVariationCompletion::STATUS_UPDATED
                ],
                'Y',
                []
            ],
            'Changed Transport Managers' => [
                'transportManagers',
                $this->getApplicationState1(),
                $this->getLicenceState2(),
                [
                    'TransportManagers' => UpdateVariationCompletion::STATUS_UNCHANGED,
                    'Undertakings' => UpdateVariationCompletion::STATUS_UPDATED
                ],
                [
                    'TransportManagers' => UpdateVariationCompletion::STATUS_UPDATED,
                    'Undertakings' => UpdateVariationCompletion::STATUS_REQUIRES_ATTENTION
                ],
                'N',
                []
            ],
            'Unchanged Transport Managers' => [
                'transportManagers',
                $this->getApplicationState2(),
                $this->getLicenceState2(),
                [
                    'TransportManagers' => UpdateVariationCompletion::STATUS_UPDATED,
                    'Undertakings' => UpdateVariationCompletion::STATUS_UPDATED
                ],
                [
                    'TransportManagers' => UpdateVariationCompletion::STATUS_UNCHANGED,
                    'Undertakings' => UpdateVariationCompletion::STATUS_UPDATED
                ],
                'Y',
                []
            ],
            'Changed Vehicles' => [
                'vehicles',
                $this->getApplicationState1(),
                $this->getLicenceState2(),
                [
                    'Vehicles' => UpdateVariationCompletion::STATUS_UNCHANGED,
                    'Undertakings' => UpdateVariationCompletion::STATUS_UPDATED
                ],
                [
                    'Vehicles' => UpdateVariationCompletion::STATUS_UPDATED,
                    'Undertakings' => UpdateVariationCompletion::STATUS_REQUIRES_ATTENTION
                ],
                'N',
                []
            ],
            'Unchanged Vehicles' => [
                'vehicles',
                $this->getApplicationState2(),
                $this->getLicenceState2(),
                [
                    'Vehicles' => UpdateVariationCompletion::STATUS_UPDATED,
                    'Undertakings' => UpdateVariationCompletion::STATUS_UPDATED
                ],
                [
                    'Vehicles' => UpdateVariationCompletion::STATUS_UNCHANGED,
                    'Undertakings' => UpdateVariationCompletion::STATUS_UPDATED
                ],
                'Y',
                []
            ],
            'Changed Conditions Undertakings' => [
                'conditionsUndertakings',
                $this->getApplicationState1(),
                $this->getLicenceState2(),
                [
                    'ConditionsUndertakings' => UpdateVariationCompletion::STATUS_UNCHANGED,
                    'Undertakings' => UpdateVariationCompletion::STATUS_UPDATED
                ],
                [
                    'ConditionsUndertakings' => UpdateVariationCompletion::STATUS_UPDATED,
                    'Undertakings' => UpdateVariationCompletion::STATUS_REQUIRES_ATTENTION
                ],
                'N',
                []
            ],
            'Unchanged Conditions Undertakings' => [
                'conditionsUndertakings',
                $this->getApplicationState2(),
                $this->getLicenceState2(),
                [
                    'ConditionsUndertakings' => UpdateVariationCompletion::STATUS_UPDATED,
                    'Undertakings' => UpdateVariationCompletion::STATUS_UPDATED
                ],
                [
                    'ConditionsUndertakings' => UpdateVariationCompletion::STATUS_UNCHANGED,
                    'Undertakings' => UpdateVariationCompletion::STATUS_UPDATED
                ],
                'Y',
                []
            ],
            'Changed Undertakings' => [
                'undertakings',
                $this->getApplicationState1(),
                $this->getLicenceState2(),
                [
                    'Undertakings' => UpdateVariationCompletion::STATUS_UNCHANGED
                ],
                [
                    'Undertakings' => UpdateVariationCompletion::STATUS_UPDATED
                ],
                'Y',
                []
            ],
            'Unchanged Undertakings' => [
                'undertakings',
                $this->getApplicationState3(),
                $this->getLicenceState2(),
                [
                    'Undertakings' => UpdateVariationCompletion::STATUS_UPDATED
                ],
                [
                    'Undertakings' => UpdateVariationCompletion::STATUS_UNCHANGED
                ],
                'N',
                []
            ],
            'Changed Business Type' => [
                'businessType',
                $this->getApplicationState1(),
                $this->getLicenceState1(),
                [
                    'BusinessType' => UpdateVariationCompletion::STATUS_UNCHANGED,
                    'Undertakings' => UpdateVariationCompletion::STATUS_UPDATED
                ],
                [
                    'BusinessType' => UpdateVariationCompletion::STATUS_UPDATED,
                    'Undertakings' => UpdateVariationCompletion::STATUS_REQUIRES_ATTENTION
                ],
                'N',
                [
                    'type' => 'foo'
                ]
            ],
            'Changed Business Details' => [
                'businessDetails',
                $this->getApplicationState1(),
                $this->getLicenceState1(),
                [
                    'BusinessDetails' => UpdateVariationCompletion::STATUS_UNCHANGED,
                    'Undertakings' => UpdateVariationCompletion::STATUS_UPDATED
                ],
                [
                    'BusinessDetails' => UpdateVariationCompletion::STATUS_UPDATED,
                    'Undertakings' => UpdateVariationCompletion::STATUS_REQUIRES_ATTENTION
                ],
                'N',
                [
                    'hasChanged' => true
                ]
            ],
            'Changed Addresses' => [
                'addresses',
                $this->getApplicationState1(),
                $this->getLicenceState1(),
                [
                    'Addresses' => UpdateVariationCompletion::STATUS_UNCHANGED,
                    'Undertakings' => UpdateVariationCompletion::STATUS_UPDATED
                ],
                [
                    'Addresses' => UpdateVariationCompletion::STATUS_UPDATED,
                    'Undertakings' => UpdateVariationCompletion::STATUS_REQUIRES_ATTENTION
                ],
                'N',
                [
                    'hasChanged' => true
                ]
            ],
            'Changed People' => [
                'people',
                $this->getApplicationState6('U'),
                $this->getLicenceState1(),
                [
                    'People' => UpdateVariationCompletion::STATUS_UNCHANGED,
                    'Undertakings' => UpdateVariationCompletion::STATUS_UPDATED
                ],
                [
                    'People' => UpdateVariationCompletion::STATUS_UPDATED,
                    'Undertakings' => UpdateVariationCompletion::STATUS_REQUIRES_ATTENTION,
                    'FinancialHistory' => UpdateVariationCompletion::STATUS_REQUIRES_ATTENTION,
                    'ConvictionsPenalties' => UpdateVariationCompletion::STATUS_REQUIRES_ATTENTION,
                ],
                'N',
                []
            ],
            'Changed People 1' => [
                'people',
                $this->getApplicationState6('D'),
                $this->getLicenceState1(),
                [
                    'People' => UpdateVariationCompletion::STATUS_UNCHANGED,
                    'Undertakings' => UpdateVariationCompletion::STATUS_UPDATED
                ],
                [
                    'People' => UpdateVariationCompletion::STATUS_UPDATED,
                    'Undertakings' => UpdateVariationCompletion::STATUS_REQUIRES_ATTENTION,
                ],
                'N',
                []
            ],
            'Changed Financial Evidence' => [
                'financialEvidence',
                $this->getApplicationState1(),
                $this->getLicenceState1(),
                [
                    'FinancialEvidence' => UpdateVariationCompletion::STATUS_UNCHANGED,
                    'Undertakings' => UpdateVariationCompletion::STATUS_UPDATED
                ],
                [
                    'FinancialEvidence' => UpdateVariationCompletion::STATUS_UPDATED,
                    'Undertakings' => UpdateVariationCompletion::STATUS_REQUIRES_ATTENTION
                ],
                'N',
                []
            ],
            'Changed Discs' => [
                'discs',
                $this->getApplicationState1(),
                $this->getLicenceState1(),
                [
                    'Discs' => UpdateVariationCompletion::STATUS_UNCHANGED,
                    'Undertakings' => UpdateVariationCompletion::STATUS_UPDATED
                ],
                [
                    'Discs' => UpdateVariationCompletion::STATUS_UPDATED,
                    'Undertakings' => UpdateVariationCompletion::STATUS_REQUIRES_ATTENTION
                ],
                'N',
                []
            ],
            'Changed Community Licences' => [
                'communityLicences',
                $this->getApplicationState1(),
                $this->getLicenceState1(),
                [
                    'CommunityLicences' => UpdateVariationCompletion::STATUS_UNCHANGED,
                    'Undertakings' => UpdateVariationCompletion::STATUS_UPDATED
                ],
                [
                    'CommunityLicences' => UpdateVariationCompletion::STATUS_UPDATED,
                    'Undertakings' => UpdateVariationCompletion::STATUS_REQUIRES_ATTENTION
                ],
                'N',
                []
            ],
            'Changed Safety' => [
                'safety',
                $this->getApplicationState1(),
                $this->getLicenceState1(),
                [
                    'Safety' => UpdateVariationCompletion::STATUS_UNCHANGED,
                    'Undertakings' => UpdateVariationCompletion::STATUS_UPDATED
                ],
                [
                    'Safety' => UpdateVariationCompletion::STATUS_UPDATED,
                    'Undertakings' => UpdateVariationCompletion::STATUS_REQUIRES_ATTENTION
                ],
                'N',
                [
                    'hasChanged' => true
                ]
            ],
            'Changed Operating Centres 1' => [
                'operatingCentres',
                $this->getApplicationState1(),
                $this->getLicenceState1(),
                [
                    'OperatingCentres' => UpdateVariationCompletion::STATUS_REQUIRES_ATTENTION,
                    'Undertakings' => UpdateVariationCompletion::STATUS_UPDATED,
                    'Vehicles' => UpdateVariationCompletion::STATUS_UNCHANGED
                ],
                [
                    'OperatingCentres' => UpdateVariationCompletion::STATUS_REQUIRES_ATTENTION,
                    'Undertakings' => UpdateVariationCompletion::STATUS_REQUIRES_ATTENTION,
                    'Vehicles' => UpdateVariationCompletion::STATUS_UNCHANGED,
                ],
                'N',
                []
            ],
            'Changed Operating Centres 2' => [
                'operatingCentres',
                $this->getApplicationState2(),
                $this->getLicenceState1(),
                [
                    'OperatingCentres' => UpdateVariationCompletion::STATUS_REQUIRES_ATTENTION,
                    'Undertakings' => UpdateVariationCompletion::STATUS_UPDATED,
                    'FinancialEvidence' => UpdateVariationCompletion::STATUS_UNCHANGED
                ],
                [
                    'OperatingCentres' => UpdateVariationCompletion::STATUS_REQUIRES_ATTENTION,
                    'Undertakings' => UpdateVariationCompletion::STATUS_REQUIRES_ATTENTION,
                    'FinancialEvidence' => UpdateVariationCompletion::STATUS_REQUIRES_ATTENTION
                ],
                'N',
                []
            ],
            'Changed Operating Centres 3' => [
                'operatingCentres',
                $this->getApplicationState4(),
                $this->getLicenceState3(),
                [
                    'OperatingCentres' => UpdateVariationCompletion::STATUS_REQUIRES_ATTENTION,
                    'Undertakings' => UpdateVariationCompletion::STATUS_UPDATED,
                    'VehiclesDeclarations' => UpdateVariationCompletion::STATUS_UNCHANGED,
                    'Discs' =>  UpdateVariationCompletion::STATUS_UNCHANGED,
                ],
                [
                    'OperatingCentres' => UpdateVariationCompletion::STATUS_REQUIRES_ATTENTION,
                    'Undertakings' => UpdateVariationCompletion::STATUS_REQUIRES_ATTENTION,
                    'VehiclesDeclarations' => UpdateVariationCompletion::STATUS_REQUIRES_ATTENTION,
                    'Discs' =>  UpdateVariationCompletion::STATUS_REQUIRES_ATTENTION,
                ],
                'N',
                []
            ],
            'Changed Operating Centres 4' => [
                'operatingCentres',
                $this->getApplicationState4(),
                $this->getLicenceState4(),
                [
                    'OperatingCentres' => UpdateVariationCompletion::STATUS_REQUIRES_ATTENTION,
                    'Undertakings' => UpdateVariationCompletion::STATUS_UPDATED,
                    'VehiclesDeclarations' => UpdateVariationCompletion::STATUS_UNCHANGED,
                ],
                [
                    'OperatingCentres' => UpdateVariationCompletion::STATUS_REQUIRES_ATTENTION,
                    'Undertakings' => UpdateVariationCompletion::STATUS_REQUIRES_ATTENTION,
                    'Vehicles' => UpdateVariationCompletion::STATUS_UNCHANGED,
                ],
                'N',
                []
            ],
            'Unchanged Operating Centres' => [
                'operatingCentres',
                $this->getApplicationState2(),
                $this->getLicenceState2(),
                [
                    'OperatingCentres' => UpdateVariationCompletion::STATUS_UPDATED,
                    'Undertakings' => UpdateVariationCompletion::STATUS_UPDATED
                ],
                [
                    'OperatingCentres' => UpdateVariationCompletion::STATUS_REQUIRES_ATTENTION,
                    'Undertakings' => UpdateVariationCompletion::STATUS_UPDATED
                ],
                'Y',
                []
            ],
            'Changed Vehicles Declarations' => [
                'vehiclesDeclarations',
                $this->getApplicationState1(),
                $this->getLicenceState1(),
                [
                    'VehiclesDeclarations' => UpdateVariationCompletion::STATUS_UNCHANGED,
                    'Undertakings' => UpdateVariationCompletion::STATUS_UPDATED
                ],
                [
                    'VehiclesDeclarations' => UpdateVariationCompletion::STATUS_UPDATED,
                    'Undertakings' => UpdateVariationCompletion::STATUS_REQUIRES_ATTENTION
                ],
                'N',
                []
            ],
            'Unchanged Vehicles Declarations' => [
                'vehiclesDeclarations',
                $this->getApplicationState2(),
                $this->getLicenceState2(),
                [
                    'VehiclesDeclarations' => UpdateVariationCompletion::STATUS_UPDATED,
                    'Undertakings' => UpdateVariationCompletion::STATUS_UPDATED
                ],
                [
                    'VehiclesDeclarations' => UpdateVariationCompletion::STATUS_UNCHANGED,
                    'Undertakings' => UpdateVariationCompletion::STATUS_UPDATED
                ],
                'Y',
                []
            ],
            'Changed Financial History' => [
                'financialHistory',
                $this->getApplicationState1(),
                $this->getLicenceState1(),
                [
                    'FinancialHistory' => UpdateVariationCompletion::STATUS_UNCHANGED,
                    'Undertakings' => UpdateVariationCompletion::STATUS_UPDATED
                ],
                [
                    'FinancialHistory' => UpdateVariationCompletion::STATUS_UPDATED,
                    'Undertakings' => UpdateVariationCompletion::STATUS_REQUIRES_ATTENTION
                ],
                'N',
                []
            ],
            'Unchanged Financial History' => [
                'financialHistory',
                $this->getApplicationState2(),
                $this->getLicenceState2(),
                [
                    'FinancialHistory' => UpdateVariationCompletion::STATUS_UPDATED,
                    'Undertakings' => UpdateVariationCompletion::STATUS_UPDATED
                ],
                [
                    'FinancialHistory' => UpdateVariationCompletion::STATUS_UNCHANGED,
                    'Undertakings' => UpdateVariationCompletion::STATUS_UPDATED
                ],
                'Y',
                []
            ],
            'Changed Convictions Penalties 1' => [
                'convictionsPenalties',
                $this->getApplicationState1(),
                $this->getLicenceState1(),
                [
                    'ConvictionsPenalties' => UpdateVariationCompletion::STATUS_UNCHANGED,
                    'Undertakings' => UpdateVariationCompletion::STATUS_UPDATED
                ],
                [
                    'ConvictionsPenalties' => UpdateVariationCompletion::STATUS_UPDATED,
                    'Undertakings' => UpdateVariationCompletion::STATUS_REQUIRES_ATTENTION
                ],
                'N',
                []
            ],
            'Changed Convictions Penalties 2' => [
                'convictionsPenalties',
                $this->getApplicationState2(),
                $this->getLicenceState1(),
                [
                    'ConvictionsPenalties' => UpdateVariationCompletion::STATUS_UNCHANGED,
                    'Undertakings' => UpdateVariationCompletion::STATUS_UPDATED
                ],
                [
                    'ConvictionsPenalties' => UpdateVariationCompletion::STATUS_UPDATED,
                    'Undertakings' => UpdateVariationCompletion::STATUS_REQUIRES_ATTENTION
                ],
                'N',
                []
            ],
            'Unchanged Convictions Penalties' => [
                'convictionsPenalties',
                $this->getApplicationState3(),
                $this->getLicenceState2(),
                [
                    'ConvictionsPenalties' => UpdateVariationCompletion::STATUS_UPDATED,
                    'Undertakings' => UpdateVariationCompletion::STATUS_UPDATED
                ],
                [
                    'ConvictionsPenalties' => UpdateVariationCompletion::STATUS_UNCHANGED,
                    'Undertakings' => UpdateVariationCompletion::STATUS_UPDATED
                ],
                'N',
                []
            ],
            'Licence Upgrade' => [
                'typeOfLicence',
                $this->getApplicationState1(),
                $this->getLicenceState3(),
                [
                    'TypeOfLicence' => UpdateVariationCompletion::STATUS_UNCHANGED,
                    'Undertakings' => UpdateVariationCompletion::STATUS_UPDATED,
                    'Addresses' => UpdateVariationCompletion::STATUS_UNCHANGED,
                    'TransportManagers' => UpdateVariationCompletion::STATUS_UNCHANGED,
                    'FinancialHistory' => UpdateVariationCompletion::STATUS_UNCHANGED,
                    'FinancialEvidence' => UpdateVariationCompletion::STATUS_UNCHANGED,
                    'ConvictionsPenalties' => UpdateVariationCompletion::STATUS_UNCHANGED,
                ],
                [
                    'TypeOfLicence' => UpdateVariationCompletion::STATUS_UPDATED,
                    'Undertakings' => UpdateVariationCompletion::STATUS_REQUIRES_ATTENTION,
                    'Addresses' => UpdateVariationCompletion::STATUS_REQUIRES_ATTENTION,
                    'TransportManagers' => UpdateVariationCompletion::STATUS_REQUIRES_ATTENTION,
                    'FinancialHistory' => UpdateVariationCompletion::STATUS_REQUIRES_ATTENTION,
                    'FinancialEvidence' => UpdateVariationCompletion::STATUS_REQUIRES_ATTENTION,
                    'ConvictionsPenalties' => UpdateVariationCompletion::STATUS_REQUIRES_ATTENTION
                ],
                'N',
                []
            ],
            'Declarations Internal unchanged' => [
                'declarationsInternal',
                $this->getApplicationState3(),
                $this->getLicenceState2(),
                [
                    'DeclarationsInternal' => UpdateVariationCompletion::STATUS_UNCHANGED,
                ],
                [
                    'DeclarationsInternal' => UpdateVariationCompletion::STATUS_UNCHANGED,
                ],
                0,
                []
            ],
            'Declarations Internal authSignature set' => [
                'declarationsInternal',
                $this->getApplicationState5(),
                $this->getLicenceState2(),
                [
                    'DeclarationsInternal' => UpdateVariationCompletion::STATUS_UNCHANGED,
                ],
                [
                    'DeclarationsInternal' => UpdateVariationCompletion::STATUS_UPDATED,
                ],
                1,
                []
            ],
        ];
    }
}
