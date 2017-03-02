<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Application;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Application\UpdateApplicationCompletion;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Domain\Service\UpdateOperatingCentreHelper;
use Dvsa\Olcs\Api\Domain\Service\VariationOperatingCentreHelper;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\Application\ApplicationOperatingCentre;
use Dvsa\Olcs\Api\Entity\EnforcementArea\EnforcementArea;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Transfer\Command\Application\UpdateOperatingCentres as Cmd;
use Dvsa\Olcs\Transfer\Command\Licence\UpdateTrafficArea;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\Application\UpdateOperatingCentres as CommandHandler;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;

/**
 * Update Operating Centres Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class UpdateOperatingCentresTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CommandHandler();
        $this->mockRepo('Application', Repository\Application::class);
        $this->mockRepo('ApplicationOperatingCentre', Repository\ApplicationOperatingCentre::class);

        $this->mockedSmServices['VariationOperatingCentreHelper'] = m::mock(VariationOperatingCentreHelper::class);
        $this->mockedSmServices['UpdateOperatingCentreHelper'] = m::mock(UpdateOperatingCentreHelper::class);
        $this->mockedSmServices['TrafficAreaValidator'] =
            m::mock(\Dvsa\Olcs\Api\Domain\Service\TrafficAreaValidator::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [];

        $this->references = [
            EnforcementArea::class => [
                'A111' => m::mock(EnforcementArea::class)
            ]
        ];

        parent::initReferences();
    }

    public function testHandleCommandPartialMissingTa()
    {
        $data = [
            'id' => 111,
            'version' => 1,
            'partial' => true,
            'partialAction' => 'add',
            'trafficArea' => null

        ];
        $command = Cmd::create($data);

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();

        /** @var ApplicationOperatingCentre $aoc */
        $aoc = m::mock(ApplicationOperatingCentre::class)->makePartial();

        $aocs = new ArrayCollection();
        $aocs->add($aoc);

        /** @var Application $application */
        $application = m::mock(Application::class)->makePartial();
        $application->setIsVariation(false);
        $application->setLicence($licence);
        $application->setOperatingCentres($aocs);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->once()
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($application);

        $this->mockedSmServices['UpdateOperatingCentreHelper']->shouldReceive('addMessage')
            ->once()
            ->with('trafficArea', 'ERR_OC_TA_1')
            ->shouldReceive('getMessages')
            ->once()
            ->andReturn(['foo' => 'bar']);

        $this->setExpectedException(ValidationException::class);

        $this->sut->handleCommand($command);
    }

    public function testHandleCommandPsvTooManyCommLic()
    {
        $data = [
            'id' => 111,
            'version' => 1,
            'partial' => false,
            'trafficArea' => null,
            'totCommunityLicences' => 10,
            'totAuthVehicles' => 8

        ];
        $command = Cmd::create($data);

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();

        $aocs = new ArrayCollection();
        $aocs->add('foo');

        /** @var Application $application */
        $application = m::mock(Application::class)->makePartial();
        $application->setId(111);
        $application->setIsVariation(false);
        $application->setLicence($licence);
        $application->shouldReceive('isPsv')->andReturn(true);
        $application->shouldReceive('canHaveCommunityLicences')->andReturn(true);
        $application->shouldReceive('getTrafficArea->getId')->andReturn('TA');
        $application->setOperatingCentres($aocs);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->once()
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($application);

        $expectedTotals = [
            'noOfOperatingCentres' => 1,
            'minVehicleAuth' => 10,
            'minTrailerAuth' => 10,
            'maxVehicleAuth' => 10,
            'maxTrailerAuth' => 10
        ];

        $this->mockedSmServices['UpdateOperatingCentreHelper']->shouldReceive('validatePsv')
            ->once()
            ->with($application, $command)
            ->shouldReceive('addMessage')
            ->once()
            ->with('totCommunityLicences', 'ERR_OC_CL_1')
            ->shouldReceive('validateTotalAuthVehicles')
            ->once()
            ->with($application, $command, $expectedTotals)
            ->shouldReceive('validateEnforcementArea')
            ->once()
            ->with($application, $command)
            ->shouldReceive('getMessages')
            ->once()
            ->andReturn(['foo' => 'bar']);

        $this->mockedSmServices['TrafficAreaValidator']->shouldReceive('validateForSameTrafficAreas')->andReturn(false);

        $aoc = [
            'noOfVehiclesRequired' => 10,
            'noOfTrailersRequired' => 10,
        ];

        $aocs = [
            $aoc
        ];

        $this->repoMap['ApplicationOperatingCentre']->shouldReceive('fetchByApplicationIdForOperatingCentres')
            ->with(111)
            ->andReturn($aocs);

        $this->setExpectedException(ValidationException::class);

        $this->sut->handleCommand($command);
    }

    public function testHandleCommandGvInvalid()
    {
        $data = [
            'id' => 111,
            'version' => 1,
            'partial' => false,
            'trafficArea' => null,
            'totCommunityLicences' => 8,
            'totAuthVehicles' => 8

        ];
        $command = Cmd::create($data);

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();

        $aocs = new ArrayCollection();
        $aocs->add('foo');

        /** @var Application $application */
        $application = m::mock(Application::class)->makePartial();
        $application->setId(111);
        $application->setIsVariation(true);
        $application->setLicence($licence);
        $application->shouldReceive('isPsv')->andReturn(false);
        $application->shouldReceive('canHaveCommunityLicences')->andReturn(true);
        $application->setOperatingCentres($aocs);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->once()
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($application);

        $expectedTotals = [
            'noOfOperatingCentres' => 1,
            'minVehicleAuth' => 10,
            'minTrailerAuth' => 10,
            'maxVehicleAuth' => 10,
            'maxTrailerAuth' => 10
        ];

        $this->mockedSmServices['UpdateOperatingCentreHelper']->shouldReceive('validateTotalAuthTrailers')
            ->once()
            ->with($command, $expectedTotals)
            ->shouldReceive('validateTotalAuthVehicles')
            ->once()
            ->with($application, $command, $expectedTotals)
            ->shouldReceive('validateEnforcementArea')
            ->once()
            ->with($application, $command)
            ->shouldReceive('getMessages')
            ->once()
            ->andReturn(['foo' => 'bar']);

        $aocs = [
            [
                'action' => 'A',
                'noOfVehiclesRequired' => 10,
                'noOfTrailersRequired' => 10,
            ],
            [
                'action' => 'D'
            ]
        ];

        $this->mockedSmServices['VariationOperatingCentreHelper']->shouldReceive('getListDataForApplication')
            ->with($application)
            ->andReturn($aocs);

        $this->setExpectedException(ValidationException::class);

        $this->sut->handleCommand($command);
    }

    public function testHandleCommandGvValid()
    {
        $data = [
            'id' => 111,
            'version' => 1,
            'partial' => false,
            'trafficArea' => 'A',
            'enforcementArea' => 'A111',
            'totCommunityLicences' => 10,
            'totAuthVehicles' => 10,
            'totAuthTrailers' => 10,
        ];
        $command = Cmd::create($data);

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();
        $licence->setId(222);
        $licence->setVersion(1);

        $aocs = new ArrayCollection();
        $aocs->add('foo');

        /** @var Application $application */
        $application = m::mock(Application::class)->makePartial();
        $application->setId(111);
        $application->setIsVariation(false);
        $application->setLicence($licence);
        $application->shouldReceive('isPsv')->andReturn(false);
        $application->shouldReceive('canHaveCommunityLicences')->andReturn(true);
        $application->setOperatingCentres($aocs);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->once()
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($application);

        $expectedTotals = [
            'noOfOperatingCentres' => 1,
            'minVehicleAuth' => 10,
            'minTrailerAuth' => 10,
            'maxVehicleAuth' => 10,
            'maxTrailerAuth' => 10
        ];

        $this->mockedSmServices['UpdateOperatingCentreHelper']->shouldReceive('validateTotalAuthTrailers')
            ->once()
            ->with($command, $expectedTotals)
            ->shouldReceive('validateTotalAuthVehicles')
            ->once()
            ->with($application, $command, $expectedTotals)
            ->shouldReceive('validateEnforcementArea')
            ->once()
            ->with($application, $command)
            ->shouldReceive('getMessages')
            ->once()
            ->andReturn([]);

        $this->mockedSmServices['TrafficAreaValidator']->shouldReceive('validateForSameTrafficAreas')->andReturn(false);

        $aocs = [
            [
                'action' => 'A',
                'noOfVehiclesRequired' => 10,
                'noOfTrailersRequired' => 10,
            ]
        ];

        $this->repoMap['ApplicationOperatingCentre']->shouldReceive('fetchByApplicationIdForOperatingCentres')
            ->with(111)
            ->andReturn($aocs);

        $data = [
            'id' => 222,
            'version' => 1,
            'trafficArea' => 'A'
        ];
        $result = new Result();
        $result->addMessage('UpdateTrafficArea');
        $this->expectedSideEffect(UpdateTrafficArea::class, $data, $result);

        $this->repoMap['Application']->shouldReceive('save')
            ->with($application);

        $data = [
            'id' => 111,
            'section' => 'operatingCentres'
        ];
        $result = new Result();
        $result->addMessage('UpdateApplicationCompletion');
        $this->expectedSideEffect(UpdateApplicationCompletion::class, $data, $result);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'UpdateTrafficArea',
                'Application record updated',
                'UpdateApplicationCompletion'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandTrafficAreaValidation()
    {
        $data = [
            'id' => 111,
            'version' => 1,
            'partial' => false,
            'trafficArea' => 'A',
            'enforcementArea' => 'A111',
            'totCommunityLicences' => 10,
            'totAuthVehicles' => 10,
            'totAuthTrailers' => 10,
        ];
        $command = Cmd::create($data);

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();
        $licence->setId(222);
        $licence->setVersion(1);

        $aocs = new ArrayCollection();
        $aocs->add('foo');

        /** @var Application $application */
        $application = m::mock(Application::class)->makePartial();
        $application->setId(111);
        $application->setIsVariation(false);
        $application->setLicence($licence);
        $application->shouldReceive('isPsv')->andReturn(false);
        $application->shouldReceive('canHaveCommunityLicences')->andReturn(true);
        $application->setOperatingCentres($aocs);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->once()
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($application);

        $expectedTotals = [
            'noOfOperatingCentres' => 1,
            'minVehicleAuth' => 10,
            'minTrailerAuth' => 10,
            'maxVehicleAuth' => 10,
            'maxTrailerAuth' => 10
        ];

        $this->mockedSmServices['UpdateOperatingCentreHelper']->shouldReceive('validateTotalAuthTrailers')
            ->once()
            ->with($command, $expectedTotals)
            ->shouldReceive('validateTotalAuthVehicles')
            ->once()
            ->with($application, $command, $expectedTotals)
            ->shouldReceive('validateEnforcementArea')
            ->once()
            ->with($application, $command)
            ->shouldReceive('addMessage')
            ->with('trafficArea', 'ERROR_1', 'TA_NAME')
            ->once()
            ->shouldReceive('getMessages')
            ->once()
            ->andReturn([]);

        $this->mockedSmServices['TrafficAreaValidator']->shouldReceive('validateForSameTrafficAreas')->andReturn(
            ['ERROR_1' => 'TA_NAME']
        );

        $aocs = [
            [
                'action' => 'A',
                'noOfVehiclesRequired' => 10,
                'noOfTrailersRequired' => 10,
            ]
        ];

        $this->repoMap['ApplicationOperatingCentre']->shouldReceive('fetchByApplicationIdForOperatingCentres')
            ->with(111)
            ->andReturn($aocs);

        $data = [
            'id' => 222,
            'version' => 1,
            'trafficArea' => 'A'
        ];
        $result = new Result();
        $result->addMessage('UpdateTrafficArea');
        $this->expectedSideEffect(UpdateTrafficArea::class, $data, $result);

        $this->repoMap['Application']->shouldReceive('save')
            ->with($application);

        $data = [
            'id' => 111,
            'section' => 'operatingCentres'
        ];
        $result = new Result();
        $result->addMessage('UpdateApplicationCompletion');
        $this->expectedSideEffect(UpdateApplicationCompletion::class, $data, $result);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'UpdateTrafficArea',
                'Application record updated',
                'UpdateApplicationCompletion'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandPsvValid()
    {
        $data = [
            'id' => 111,
            'version' => 1,
            'partial' => false,
            'trafficArea' => 'A',
            'enforcementArea' => 'A111',
            'totCommunityLicences' => 10,
            'totAuthSmallVehicles' => 4,
            'totAuthMediumVehicles' => 3,
            'totAuthLargeVehicles' => 3,
            'totAuthVehicles' => 10,
            'totAuthTrailers' => 10,
        ];
        $command = Cmd::create($data);

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();
        $licence->setId(222);
        $licence->setVersion(1);

        $aocs = new ArrayCollection();
        $aocs->add('foo');

        /** @var Application $application */
        $application = m::mock(Application::class)->makePartial();
        $application->setId(111);
        $application->setIsVariation(true);
        $application->setLicence($licence);
        $application->shouldReceive('isPsv')->andReturn(true);
        $application->shouldReceive('canHaveCommunityLicences')->andReturn(true);
        $application->shouldReceive('canHaveLargeVehicles')->andReturn(true);
        $application->setOperatingCentres($aocs);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->once()
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($application);

        $expectedTotals = [
            'noOfOperatingCentres' => 1,
            'minVehicleAuth' => 10,
            'minTrailerAuth' => 10,
            'maxVehicleAuth' => 10,
            'maxTrailerAuth' => 10
        ];

        $this->mockedSmServices['UpdateOperatingCentreHelper']->shouldReceive('validatePsv')
            ->once()
            ->with($application, $command)
            ->shouldReceive('validateTotalAuthVehicles')
            ->once()
            ->with($application, $command, $expectedTotals)
            ->shouldReceive('validateEnforcementArea')
            ->once()
            ->with($application, $command)
            ->shouldReceive('getMessages')
            ->once()
            ->andReturn([]);

        $aocs = [
            [
                'action' => 'A',
                'noOfVehiclesRequired' => 10,
                'noOfTrailersRequired' => 10,
            ]
        ];

        $this->mockedSmServices['VariationOperatingCentreHelper']->shouldReceive('getListDataForApplication')
            ->with($application)
            ->andReturn($aocs);

        $this->repoMap['Application']->shouldReceive('save')
            ->with($application);

        $data = [
            'id' => 111,
            'section' => 'operatingCentres'
        ];
        $result = new Result();
        $result->addMessage('UpdateApplicationCompletion');
        $this->expectedSideEffect(UpdateApplicationCompletion::class, $data, $result);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'Application record updated',
                'UpdateApplicationCompletion'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandPsvValidVariationWithTa()
    {
        $data = [
            'id' => 111,
            'version' => 1,
            'partial' => false,
            'trafficArea' => 'A',
            'enforcementArea' => 'A111',
            'totCommunityLicences' => 10,
            'totAuthSmallVehicles' => 4,
            'totAuthMediumVehicles' => 3,
            'totAuthLargeVehicles' => 3,
            'totAuthVehicles' => 10,
            'totAuthTrailers' => 10,
        ];
        $command = Cmd::create($data);

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();
        $licence->setId(222);
        $licence->setVersion(1);

        $aocs = new ArrayCollection();
        $aocs->add('foo');

        /** @var Application $application */
        $application = m::mock(Application::class)->makePartial();
        $application->setId(111);
        $application->setIsVariation(true);
        $application->setLicence($licence);
        $application->shouldReceive('isPsv')->andReturn(true);
        $application->shouldReceive('canHaveCommunityLicences')->andReturn(true);
        $application->shouldReceive('canHaveLargeVehicles')->andReturn(true);
        $application->shouldReceive('getTrafficArea')->andReturn('anything');
        $application->setOperatingCentres($aocs);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->once()
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($application);

        $expectedTotals = [
            'noOfOperatingCentres' => 1,
            'minVehicleAuth' => 10,
            'minTrailerAuth' => 10,
            'maxVehicleAuth' => 10,
            'maxTrailerAuth' => 10
        ];

        $this->mockedSmServices['UpdateOperatingCentreHelper']->shouldReceive('validatePsv')
            ->once()
            ->with($application, $command)
            ->shouldReceive('validateTotalAuthVehicles')
            ->once()
            ->with($application, $command, $expectedTotals)
            ->shouldReceive('validateEnforcementArea')
            ->once()
            ->with($application, $command)
            ->shouldReceive('getMessages')
            ->once()
            ->andReturn([]);

        $aocs = [
            [
                'action' => 'A',
                'noOfVehiclesRequired' => 10,
                'noOfTrailersRequired' => 10,
            ]
        ];

        $this->mockedSmServices['VariationOperatingCentreHelper']->shouldReceive('getListDataForApplication')
            ->with($application)
            ->andReturn($aocs);

        $this->repoMap['Application']->shouldReceive('save')
            ->with($application);

        $data = [
            'id' => 111,
            'section' => 'operatingCentres'
        ];
        $result = new Result();
        $result->addMessage('UpdateApplicationCompletion');
        $this->expectedSideEffect(UpdateApplicationCompletion::class, $data, $result);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'Application record updated',
                'UpdateApplicationCompletion'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());

        $this->assertSame($this->references[EnforcementArea::class]['A111'], $licence->getEnforcementArea());
    }
}
