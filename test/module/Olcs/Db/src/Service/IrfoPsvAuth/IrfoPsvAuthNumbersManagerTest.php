<?php


namespace OlcsTest\Db\Service\IrfoPsvAuth;

use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Dvsa\Olcs\Api\Entity\Irfo\IrfoPsvAuth;
use Dvsa\Olcs\Api\Entity\Irfo\IrfoPsvAuthNumber;
use Olcs\Db\Service\IrfoPsvAuth\IrfoPsvAuthNumbersManager;
use Mockery as m;

/**
 * Class IrfoPsvAuthNumbersManagerTest
 * @package OlcsTest\Db\Service\IrfoPsvAuth
 */
class IrfoPsvAuthNumbersManagerTest extends TestCase
{
    public function testCreateService()
    {
        $mockDs = m::mock('Olcs\Db\Service\ServiceAbstract');

        $mockSl = m::mock('Zend\ServiceManager\ServiceLocatorInterface');
        $mockSl->shouldReceive('get')->with('ServiceFactory')->andReturnSelf();
        $mockSl->shouldReceive('getService')->with('IrfoPsvAuthNumber')->andReturn($mockDs);

        $sut = new IrfoPsvAuthNumbersManager();
        $service = $sut->createService($mockSl);

        $this->assertInstanceOf('Olcs\Db\Service\IrfoPsvAuth\IrfoPsvAuthNumbersManager', $service);
        $this->assertSame($mockDs, $service->getDataService());
    }

    public function testProcessIrfoPsvAuthNumbers()
    {
        $irfoPsvAuth = new IrfoPsvAuth();
        $irfoPsvAuth->setId(1);

        $mockDataService = m::mock('Olcs\Db\Service\ServiceAbstract');
        $mockDataService->shouldReceive('update')->once()->with(1, ['id' => 1, 'name' => 'updated number']);
        $mockDataService->shouldReceive('delete')->once()->with(2);
        $mockDataService->shouldReceive('delete')->once()->with(3);
        $mockDataService->shouldReceive('create')->once()
            ->with(
                [
                    'name' => 'new number',
                    'irfoPsvAuth' => $irfoPsvAuth,
                    'id' => null,
                    'version' => null
                ]
            )
            ->andReturn(4);

        $existing1 = new IrfoPsvAuthNumber();
        $existing1->setId(1);
        $existing1->setIrfoPsvAuth($irfoPsvAuth);

        $existing2 = new IrfoPsvAuthNumber();
        $existing2->setId(2);
        $existing2->setIrfoPsvAuth($irfoPsvAuth);

        $existing3 = new IrfoPsvAuthNumber();
        $existing3->setId(3);
        $existing3->setIrfoPsvAuth($irfoPsvAuth);

        $irfoPsvAuth->getIrfoPsvAuthNumbers()->add($existing1);
        $irfoPsvAuth->getIrfoPsvAuthNumbers()->add($existing2);
        $irfoPsvAuth->getIrfoPsvAuthNumbers()->add($existing3);

        $data = [
            ['id' => 1, 'name' => 'updated number'],
            ['id' => 2, 'name' => ''],
            ['name' => 'new number']
        ];

        $sut = new IrfoPsvAuthNumbersManager();

        $sut->setDataService($mockDataService);

        $response = $sut->processIrfoPsvAuthNumbers($irfoPsvAuth, $data);

        $this->assertEquals([1, 4], $response);
    }
}
