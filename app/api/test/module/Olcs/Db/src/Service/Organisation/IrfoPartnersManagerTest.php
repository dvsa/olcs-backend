<?php


namespace OlcsTest\Db\Service\Organisation;

use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Olcs\Db\Entity\Organisation;
use Olcs\Db\Entity\IrfoPartner;
use Olcs\Db\Service\Organisation\IrfoPartnersManager;
use Mockery as m;

/**
 * Class IrfoPartnersManagerTest
 * @package OlcsTest\Db\Service\Organisation
 */
class IrfoPartnersManagerTest extends TestCase
{
    public function testCreateService()
    {
        $mockDs = m::mock('Olcs\Db\Service\ServiceAbstract');

        $mockSl = m::mock('Zend\ServiceManager\ServiceLocatorInterface');
        $mockSl->shouldReceive('get')->with('ServiceFactory')->andReturnSelf();
        $mockSl->shouldReceive('getService')->with('IrfoPartner')->andReturn($mockDs);

        $sut = new IrfoPartnersManager();
        $service = $sut->createService($mockSl);

        $this->assertInstanceOf('Olcs\Db\Service\Organisation\IrfoPartnersManager', $service);
        $this->assertSame($mockDs, $service->getDataService());
    }

    public function testProcessIrfoPartners()
    {
        $organisation = new Organisation();
        $organisation->setId(1);

        $mockDataService = m::mock('Olcs\Db\Service\ServiceAbstract');
        $mockDataService->shouldReceive('update')->once()->with(1, ['id' => 1, 'name' => 'updated partner']);
        $mockDataService->shouldReceive('delete')->once()->with(2);
        $mockDataService->shouldReceive('delete')->once()->with(3);
        $mockDataService->shouldReceive('create')->once()
            ->with(
                [
                    'name' => 'new partner',
                    'organisation' => $organisation,
                    'id' => null,
                    'version' => null
                ]
            )
            ->andReturn(4);

        $existing1 = new IrfoPartner();
        $existing1->setId(1);
        $existing1->setOrganisation($organisation);

        $existing2 = new IrfoPartner();
        $existing2->setId(2);
        $existing2->setOrganisation($organisation);

        $existing3 = new IrfoPartner();
        $existing3->setId(3);
        $existing3->setOrganisation($organisation);

        $organisation->getIrfoPartners()->add($existing1);
        $organisation->getIrfoPartners()->add($existing2);
        $organisation->getIrfoPartners()->add($existing3);

        $data = [
            ['id' => 1, 'name' => 'updated partner'],
            ['id' => 2, 'name' => ''],
            ['name' => 'new partner']
        ];

        $sut = new IrfoPartnersManager();

        $sut->setDataService($mockDataService);

        $response = $sut->processIrfoPartners($organisation, $data);

        $this->assertEquals([1, 4], $response);
    }
}
