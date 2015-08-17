<?php

/**
 * Handle Oc Variation Fees Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Application;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Domain\Command\Application\CreateApplicationFee;
use Dvsa\Olcs\Api\Domain\Command\Fee\CancelFee;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\Application\ApplicationOperatingCentre;
use Dvsa\Olcs\Api\Entity\Fee\Fee;
use Dvsa\Olcs\Api\Entity\Fee\FeeType;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Licence\LicenceOperatingCentre;
use Dvsa\Olcs\Api\Entity\OperatingCentre\OperatingCentre;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\Application\HandleOcVariationFees as CommandHandler;
use Dvsa\Olcs\Api\Domain\Command\Application\HandleOcVariationFees as Cmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository;

/**
 * Handle Oc Variation Fees Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class HandleOcVariationFeesTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CommandHandler();
        $this->mockRepo('Application', Repository\Application::class);
        $this->mockRepo('Fee', Repository\Fee::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [];

        parent::initReferences();
    }

    public function testHandleCommandWithAddedOcWithoutFees()
    {
        $data = [];
        $command = Cmd::create($data);

        /** @var LicenceOperatingCentre $loc */
        $loc = m::mock(LicenceOperatingCentre::class)->makePartial();
        $locs = new ArrayCollection();
        $locs->add($loc);

        /** @var ApplicationOperatingCentre $aoc */
        $aoc = m::mock(ApplicationOperatingCentre::class)->makePartial();
        $aoc->setAction('A');
        $aocs = new ArrayCollection();
        $aocs->add($aoc);

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();
        $licence->setOperatingCentres($locs);

        /** @var Application $application */
        $application = m::mock(Application::class)->makePartial();
        $application->setOperatingCentres($aocs);
        $application->setLicence($licence);
        $application->setId(111);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($application);

        $fees = [];

        $this->repoMap['Fee']->shouldReceive('fetchOutstandingFeesByApplicationId')
            ->with(111)
            ->andReturn($fees);

        $data = [
            'id' => 111,
            'feeTypeFeeType' => FeeType::FEE_TYPE_VAR,
            'description' => null
        ];
        $result = new Result();
        $result->addMessage('CreateApplicationFee');
        $this->expectedSideEffect(CreateApplicationFee::class, $data, $result);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'CreateApplicationFee'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandWithAddedOcWithFees()
    {
        $data = [];
        $command = Cmd::create($data);

        /** @var LicenceOperatingCentre $loc */
        $loc = m::mock(LicenceOperatingCentre::class)->makePartial();
        $locs = new ArrayCollection();
        $locs->add($loc);

        /** @var ApplicationOperatingCentre $aoc */
        $aoc = m::mock(ApplicationOperatingCentre::class)->makePartial();
        $aoc->setAction('A');
        $aocs = new ArrayCollection();
        $aocs->add($aoc);

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();
        $licence->setOperatingCentres($locs);

        /** @var Application $application */
        $application = m::mock(Application::class)->makePartial();
        $application->setOperatingCentres($aocs);
        $application->setLicence($licence);
        $application->setId(111);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($application);

        $fees = [
            'foo'
        ];

        $this->repoMap['Fee']->shouldReceive('fetchOutstandingFeesByApplicationId')
            ->with(111)
            ->andReturn($fees);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => []
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandWithUpdatedOcWithIncreasedVehiclesWithoutFees()
    {
        $data = [];
        $command = Cmd::create($data);

        /** @var OperatingCentre $oc */
        $oc = m::mock(OperatingCentre::class)->makePartial();

        /** @var LicenceOperatingCentre $loc */
        $loc = m::mock(LicenceOperatingCentre::class)->makePartial();
        $loc->setOperatingCentre($oc);
        $loc->setNoOfVehiclesRequired(10);
        $locs = new ArrayCollection();
        $locs->add($loc);

        /** @var ApplicationOperatingCentre $aoc */
        $aoc = m::mock(ApplicationOperatingCentre::class)->makePartial();
        $aoc->setAction('U');
        $aoc->setOperatingCentre($oc);
        $aoc->setNoOfVehiclesRequired(15);
        $aocs = new ArrayCollection();
        $aocs->add($aoc);

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();
        $licence->setOperatingCentres($locs);

        /** @var Application $application */
        $application = m::mock(Application::class)->makePartial();
        $application->setOperatingCentres($aocs);
        $application->setLicence($licence);
        $application->setId(111);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($application);

        $fees = [];

        $this->repoMap['Fee']->shouldReceive('fetchOutstandingFeesByApplicationId')
            ->with(111)
            ->andReturn($fees);

        $data = [
            'id' => 111,
            'feeTypeFeeType' => FeeType::FEE_TYPE_VAR,
            'description' => null
        ];
        $result = new Result();
        $result->addMessage('CreateApplicationFee');
        $this->expectedSideEffect(CreateApplicationFee::class, $data, $result);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'CreateApplicationFee'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandWithUpdatedOcWithIncreasedTrailersWithoutFees()
    {
        $data = [];
        $command = Cmd::create($data);

        /** @var OperatingCentre $oc */
        $oc = m::mock(OperatingCentre::class)->makePartial();

        /** @var LicenceOperatingCentre $loc */
        $loc = m::mock(LicenceOperatingCentre::class)->makePartial();
        $loc->setOperatingCentre($oc);
        $loc->setNoOfVehiclesRequired(10);
        $loc->setNoOfTrailersRequired(10);
        $locs = new ArrayCollection();
        $locs->add($loc);

        /** @var ApplicationOperatingCentre $aoc */
        $aoc = m::mock(ApplicationOperatingCentre::class)->makePartial();
        $aoc->setAction('U');
        $aoc->setOperatingCentre($oc);
        $aoc->setNoOfVehiclesRequired(10);
        $aoc->setNoOfTrailersRequired(20);
        $aocs = new ArrayCollection();
        $aocs->add($aoc);

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();
        $licence->setOperatingCentres($locs);

        /** @var Application $application */
        $application = m::mock(Application::class)->makePartial();
        $application->setOperatingCentres($aocs);
        $application->setLicence($licence);
        $application->setId(111);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($application);

        $fees = [];

        $this->repoMap['Fee']->shouldReceive('fetchOutstandingFeesByApplicationId')
            ->with(111)
            ->andReturn($fees);

        $data = [
            'id' => 111,
            'feeTypeFeeType' => FeeType::FEE_TYPE_VAR,
            'description' => null
        ];
        $result = new Result();
        $result->addMessage('CreateApplicationFee');
        $this->expectedSideEffect(CreateApplicationFee::class, $data, $result);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'CreateApplicationFee'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandWithUpdatedOcWithoutIncreaseWithoutFees()
    {
        $data = [];
        $command = Cmd::create($data);

        /** @var OperatingCentre $oc */
        $oc = m::mock(OperatingCentre::class)->makePartial();
        /** @var OperatingCentre $oc2 */
        $oc2 = m::mock(OperatingCentre::class)->makePartial();

        /** @var LicenceOperatingCentre $loc1 */
        $loc1 = m::mock(LicenceOperatingCentre::class)->makePartial();
        $loc1->setOperatingCentre($oc2);
        /** @var LicenceOperatingCentre $loc2 */
        $loc2 = m::mock(LicenceOperatingCentre::class)->makePartial();
        $loc2->setOperatingCentre($oc);
        $loc2->setNoOfVehiclesRequired(10);
        $loc2->setNoOfTrailersRequired(10);
        $locs = new ArrayCollection();
        $locs->add($loc1);
        $locs->add($loc2);

        /** @var ApplicationOperatingCentre $aoc */
        $aoc = m::mock(ApplicationOperatingCentre::class)->makePartial();
        $aoc->setAction('U');
        $aoc->setOperatingCentre($oc);
        $aoc->setNoOfVehiclesRequired(10);
        $aoc->setNoOfTrailersRequired(10);
        $aocs = new ArrayCollection();
        $aocs->add($aoc);

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();
        $licence->setOperatingCentres($locs);

        /** @var Application $application */
        $application = m::mock(Application::class)->makePartial();
        $application->setOperatingCentres($aocs);
        $application->setLicence($licence);
        $application->setId(111);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($application);

        $fees = [];

        $this->repoMap['Fee']->shouldReceive('fetchOutstandingFeesByApplicationId')
            ->with(111)
            ->andReturn($fees);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => []
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandWithUpdatedOcWithoutIncreaseWithFees()
    {
        $data = [];
        $command = Cmd::create($data);

        /** @var OperatingCentre $oc */
        $oc = m::mock(OperatingCentre::class)->makePartial();
        /** @var OperatingCentre $oc2 */
        $oc2 = m::mock(OperatingCentre::class)->makePartial();

        /** @var LicenceOperatingCentre $loc1 */
        $loc1 = m::mock(LicenceOperatingCentre::class)->makePartial();
        $loc1->setOperatingCentre($oc2);
        /** @var LicenceOperatingCentre $loc2 */
        $loc2 = m::mock(LicenceOperatingCentre::class)->makePartial();
        $loc2->setOperatingCentre($oc);
        $loc2->setNoOfVehiclesRequired(10);
        $loc2->setNoOfTrailersRequired(10);
        $locs = new ArrayCollection();
        $locs->add($loc1);
        $locs->add($loc2);

        /** @var ApplicationOperatingCentre $aoc */
        $aoc = m::mock(ApplicationOperatingCentre::class)->makePartial();
        $aoc->setAction('U');
        $aoc->setOperatingCentre($oc);
        $aoc->setNoOfVehiclesRequired(10);
        $aoc->setNoOfTrailersRequired(10);
        $aocs = new ArrayCollection();
        $aocs->add($aoc);

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();
        $licence->setOperatingCentres($locs);

        /** @var Application $application */
        $application = m::mock(Application::class)->makePartial();
        $application->setOperatingCentres($aocs);
        $application->setLicence($licence);
        $application->setId(111);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($application);

        /** @var Fee $fee */
        $fee = m::mock(Fee::class)->makePartial();
        $fee->setId(123);

        $fees = [
            $fee
        ];

        $this->repoMap['Fee']->shouldReceive('fetchOutstandingFeesByApplicationId')
            ->with(111)
            ->andReturn($fees);

        $data = [
            'id' => 123
        ];
        $result = new Result();
        $result->addMessage('CancelFee');
        $this->expectedSideEffect(CancelFee::class, $data, $result);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                '1 Fee(s) cancelled'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
