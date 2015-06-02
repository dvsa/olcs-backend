<?php


namespace OlcsTest\Db\Service\BusReg;

use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Dvsa\Olcs\Api\Entity\Bus\BusReg;
use Dvsa\Olcs\Api\Entity\Bus\BusRegOtherService;
use Olcs\Db\Service\BusReg\OtherServicesManager;
use Mockery as m;

/**
 * Class OtherServicesManagerTest
 * @package OlcsTest\Db\Service\BusReg
 */
class OtherServicesManagerTest extends TestCase
{
    public function testCreateService()
    {
        $mockDs = m::mock('Olcs\Db\Service\ServiceAbstract');

        $mockSl = m::mock('Zend\ServiceManager\ServiceLocatorInterface');
        $mockSl->shouldReceive('get')->with('ServiceFactory')->andReturnSelf();
        $mockSl->shouldReceive('getService')->with('BusRegOtherService')->andReturn($mockDs);

        $sut = new OtherServicesManager();
        $service = $sut->createService($mockSl);

        $this->assertInstanceOf('Olcs\Db\Service\BusReg\OtherServicesManager', $service);
        $this->assertSame($mockDs, $service->getDataService());
    }

    public function testProcessOtherServiceNumbers()
    {
        $busReg = new BusReg();
        $busReg->setId(1);

        $mockDataService = m::mock('Olcs\Db\Service\ServiceAbstract');
        $mockDataService->shouldReceive('update')->with(1, ['id' => 1, 'serviceNo' => 'abc']);
        $mockDataService->shouldReceive('create')->with(['serviceNo' => 54, 'busReg' => $busReg])-> andReturn(3);
        $mockDataService->shouldReceive('delete')->with(2);

        $existing1 = new BusRegOtherService();
        $existing1->setId(1);
        $existing1->setBusReg($busReg);

        $existing2 = new BusRegOtherService();
        $existing2->setId(2);
        $existing2->setBusReg($busReg);

        $busReg->getOtherServices()->add($existing1);
        $busReg->getOtherServices()->add($existing2);

        $data = [
            ['id' => 1, 'serviceNo' => 'abc'],
            ['serviceNo' => 54]
        ];

        $sut = new OtherServicesManager();

        $sut->setDataService($mockDataService);

        $response = $sut->processOtherServiceNumbers($busReg, $data);

        $this->assertEquals([1, 3], $response);
    }
}
