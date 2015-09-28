<?php

/**
 * UpdateTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\PrivateHireLicence;

use Dvsa\Olcs\Api\Domain\CommandHandler\PrivateHireLicence\Update as CommandHandler;
use Dvsa\Olcs\Transfer\Command\PrivateHireLicence\Update as Command;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;
use Dvsa\Olcs\Api\Entity\ContactDetails\Country;
use Dvsa\Olcs\Api\Domain\Command\Task\CreateTask as CreateTaskCmd;
use Dvsa\Olcs\Api\Entity\System\Category as CategoryEntity;
use Dvsa\Olcs\Api\Entity\User\Permission;
use ZfcRbac\Service\AuthorizationService;
use Dvsa\Olcs\Api\Domain\Command\Result;

/**
 * UpdateTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class UpdateTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CommandHandler();
        $this->mockRepo('PrivateHireLicence', \Dvsa\Olcs\Api\Domain\Repository\PrivateHireLicence::class);
        $this->mockRepo('ContactDetails', \Dvsa\Olcs\Api\Domain\Repository\ContactDetails::class);
        $this->mockRepo('AdminAreaTrafficArea', \Dvsa\Olcs\Api\Domain\Repository\AdminAreaTrafficArea::class);
        $this->mockedSmServices = [
            AuthorizationService::class => m::mock(AuthorizationService::class),
            'AddressService' => m::mock(\Dvsa\Olcs\Address\Service\AddressInterface::class)
        ];

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->references = [
            Country::class => [
                'CC' => m::mock(Country::class)
            ],
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $params =[
            'id' => 323,
            'version' => 323,
            'privateHireLicenceNo' => 'TOPDOG 1',
            'councilName' => 'Leeds',
            'address' => [
                'addressLine1' => 'LINE 1',
                'addressLine2' => 'LINE 2',
                'addressLine3' => 'LINE 3',
                'addressLine4' => 'LINE 4',
                'town' => 'TOWN',
                'postcode' => 'S1 4QT',
                'countryCode' => 'CC',
            ],
            'lva' => 'licence',
            'licence' => 1
        ];
        $command = Command::create($params);

        $cd = new \Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails(new \Dvsa\Olcs\Api\Entity\System\RefData());
        $cd->setAddress(new \Dvsa\Olcs\Api\Entity\ContactDetails\Address());
        $phl = new \Dvsa\Olcs\Api\Entity\Licence\PrivateHireLicence();
        $phl->setId(564)
            ->setContactDetails($cd);

        $this->repoMap['PrivateHireLicence']->shouldReceive('fetchUsingId')->once()->andReturn($phl);

        $this->mockedSmServices['AddressService']->shouldReceive('fetchTrafficAreaByPostcode')
            ->with('S1 4QT', $this->repoMap['AdminAreaTrafficArea'])->once()->andReturn(null);

        $this->repoMap['PrivateHireLicence']->shouldReceive('save')->once()->andReturnUsing(
            function (\Dvsa\Olcs\Api\Entity\Licence\PrivateHireLicence $savePhl) use ($params) {
                $this->assertSame($params['privateHireLicenceNo'], $savePhl->getPrivateHireLicenceNo());
            }
        );

        $this->repoMap['ContactDetails']->shouldReceive('save')->once()->andReturnUsing(
            function (\Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails $cd) use ($params) {
                $this->assertSame($params['councilName'], $cd->getDescription());
                $this->assertSame($params['address']['addressLine1'], $cd->getAddress()->getAddressLine1());
                $this->assertSame($params['address']['addressLine2'], $cd->getAddress()->getAddressLine2());
                $this->assertSame($params['address']['addressLine3'], $cd->getAddress()->getAddressLine3());
                $this->assertSame($params['address']['addressLine4'], $cd->getAddress()->getAddressLine4());
                $this->assertSame($params['address']['town'], $cd->getAddress()->getTown());
                $this->assertSame($params['address']['postcode'], $cd->getAddress()->getPostcode());
                $this->assertSame($this->references[Country::class]['CC'], $cd->getAddress()->getCountryCode());
            }
        );

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('isGranted')
            ->once()
            ->with(Permission::SELFSERVE_USER, null)
            ->andReturn(true);
        $data = [
            'licence' => 1,
            'category' => CategoryEntity::CATEGORY_APPLICATION,
            'subCategory' => CategoryEntity::TASK_SUB_CATEGORY_CHANGE_TO_TAXI_PHV_DIGITAL,
            'description' => 'Taxi licence updated - ' . 'TOPDOG 1',
            'isClosed' => 0,
            'urgent' => 0,
            'actionDate' => null,
            'assignedToUser' => null,
            'assignedToTeam' => null,
            'application' => null,
            'busReg' => null,
            'case' => null,
            'transportManager' => null,
            'irfoOrganisation' => null
        ];
        $this->expectedSideEffect(CreateTaskCmd::class, $data, new Result());

        $response = $this->sut->handleCommand($command);

        $this->assertSame(['privateHireLicence' => 564], $response->getIds());
        $this->assertSame(['PrivateHireLicence ID 564 updated'], $response->getMessages());
    }

    public function testHandleCommandTrafficAreaUpdate()
    {
        $params =[
            'id' => 323,
            'version' => 323,
            'privateHireLicenceNo' => 'TOPDOG 1',
            'councilName' => 'Leeds',
            'address' => [
                'addressLine1' => 'LINE 1',
                'addressLine2' => 'LINE 2',
                'addressLine3' => 'LINE 3',
                'addressLine4' => 'LINE 4',
                'town' => 'TOWN',
                'postcode' => 'S1 4QT',
                'countryCode' => 'CC',
            ],
            'lva' => 'licence',
            'licence' => 1
        ];
        $command = Command::create($params);

        $trafficArea = new \Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea();
        $trafficArea->setId('TA');

        $licence = $this->getTestingLicence()->setId(323);
        $cd = new \Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails(new \Dvsa\Olcs\Api\Entity\System\RefData());
        $cd->setAddress(new \Dvsa\Olcs\Api\Entity\ContactDetails\Address());
        $phl = new \Dvsa\Olcs\Api\Entity\Licence\PrivateHireLicence();
        $phl->setId(564)
            ->setContactDetails($cd);
        $phl->setLicence($licence);

        $this->repoMap['PrivateHireLicence']->shouldReceive('fetchUsingId')->once()->andReturn($phl);

        $this->mockedSmServices['AddressService']->shouldReceive('fetchTrafficAreaByPostcode')
            ->with('S1 4QT', $this->repoMap['AdminAreaTrafficArea'])->once()->andReturn($trafficArea);

        $this->repoMap['PrivateHireLicence']->shouldReceive('save')->once();

        $this->repoMap['ContactDetails']->shouldReceive('save')->once();

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('isGranted')
            ->once()
            ->andReturn(false);

        $this->expectedSideEffect(
            \Dvsa\Olcs\Transfer\Command\Licence\UpdateTrafficArea::class,
            ['id' => 323, 'version' => 1, 'trafficArea' => 'TA'],
            new Result()
        );

        $this->sut->handleCommand($command);
    }

    public function testHandleCommandTrafficAreaWithOnePhl()
    {
        $params =[
            'id' => 323,
            'version' => 323,
            'privateHireLicenceNo' => 'TOPDOG 1',
            'councilName' => 'Leeds',
            'address' => [
                'addressLine1' => 'LINE 1',
                'addressLine2' => 'LINE 2',
                'addressLine3' => 'LINE 3',
                'addressLine4' => 'LINE 4',
                'town' => 'TOWN',
                'postcode' => 'S1 4QT',
                'countryCode' => 'CC',
            ],
            'lva' => 'licence',
            'licence' => 1
        ];
        $command = Command::create($params);

        $trafficArea1 = new \Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea();
        $trafficArea1->setId('TA1');

        $trafficArea2 = new \Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea();
        $trafficArea2->setId('TA2');

        $licence = $this->getTestingLicence()->setId(323)->setTrafficArea($trafficArea2);
        $cd = new \Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails(new \Dvsa\Olcs\Api\Entity\System\RefData());
        $cd->setAddress(new \Dvsa\Olcs\Api\Entity\ContactDetails\Address());
        $phl = new \Dvsa\Olcs\Api\Entity\Licence\PrivateHireLicence();
        $phl->setId(564)
            ->setContactDetails($cd);
        $phl->setLicence($licence);
        $licence->addPrivateHireLicences($phl);

        $this->repoMap['PrivateHireLicence']->shouldReceive('fetchUsingId')->once()->andReturn($phl);

        $this->mockedSmServices['AddressService']->shouldReceive('fetchTrafficAreaByPostcode')
            ->with('S1 4QT', $this->repoMap['AdminAreaTrafficArea'])->once()->andReturn($trafficArea1);

        $this->repoMap['PrivateHireLicence']->shouldReceive('save')->once();

        $this->repoMap['ContactDetails']->shouldReceive('save')->once();

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('isGranted')
            ->once()
            ->andReturn(false);

        $this->expectedSideEffect(
            \Dvsa\Olcs\Transfer\Command\Licence\UpdateTrafficArea::class,
            ['id' => 323, 'version' => 1, 'trafficArea' => 'TA1'],
            new Result()
        );

        $this->sut->handleCommand($command);
    }

    public function testHandleCommandTrafficAreaValidationError()
    {
        $params =[
            'id' => 323,
            'version' => 323,
            'privateHireLicenceNo' => 'TOPDOG 1',
            'councilName' => 'Leeds',
            'address' => [
                'addressLine1' => 'LINE 1',
                'addressLine2' => 'LINE 2',
                'addressLine3' => 'LINE 3',
                'addressLine4' => 'LINE 4',
                'town' => 'TOWN',
                'postcode' => 'S1 4QT',
                'countryCode' => 'CC',
            ],
            'lva' => 'licence',
            'licence' => 1
        ];
        $command = Command::create($params);

        $trafficArea1 = new \Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea();
        $trafficArea1->setId('TA1');

        $trafficArea2 = new \Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea();
        $trafficArea2->setId('TA2');

        $licence = $this->getTestingLicence()->setId(323)->setTrafficArea($trafficArea2);
        $cd = new \Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails(new \Dvsa\Olcs\Api\Entity\System\RefData());
        $cd->setAddress(new \Dvsa\Olcs\Api\Entity\ContactDetails\Address());
        $phl = new \Dvsa\Olcs\Api\Entity\Licence\PrivateHireLicence();
        $phl->setId(564)->setContactDetails($cd);
        $phl->setLicence($licence);
        $licence->addPrivateHireLicences($phl);
        $phl2 = new \Dvsa\Olcs\Api\Entity\Licence\PrivateHireLicence();
        $licence->addPrivateHireLicences($phl2);

        $this->repoMap['PrivateHireLicence']->shouldReceive('fetchUsingId')->once()->andReturn($phl);

        $this->mockedSmServices['AddressService']->shouldReceive('fetchTrafficAreaByPostcode')
            ->with('S1 4QT', $this->repoMap['AdminAreaTrafficArea'])->once()->andReturn($trafficArea1);

        try {
            $this->sut->handleCommand($command);
            $this->fail('Exception should have been thrown');
        } catch (\Dvsa\Olcs\Api\Domain\Exception\ValidationException $e) {
            $this->assertArrayHasKey('PHL_INVALID_TA', $e->getMessages());
        }
    }
}
