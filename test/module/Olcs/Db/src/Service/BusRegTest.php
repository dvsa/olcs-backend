<?php


namespace OlcsTest\Db\Service;

use Dvsa\OlcsTest\Api\Entity\Bus\BusRegOtherServiceTest;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Mockery as m;
use Olcs\Db\Service\BusReg;
use OlcsTest\Bootstrap;
use Dvsa\Olcs\Api\Entity\Bus\BusReg as BusRegEntity;

/**
 * Class BusRegTest
 * @package OlcsTest\Db\Service
 */
class BusRegTest extends TestCase
{
    protected $sut;

    protected $sm;

    protected $em;

    protected function mockHydrator()
    {
        $mockDoctrineObject = $this->createPartialMock('\stdClass', ['hydrate']);

        $mockHydratorManager = $this->createPartialMock('\stdClass', ['get']);
        $mockHydratorManager->expects($this->once())
            ->method('get')
            ->with('DoctrineModule\Stdlib\Hydrator\DoctrineObject')
            ->will($this->returnValue($mockDoctrineObject));

        $this->sm->setService('HydratorManager', $mockHydratorManager);

        return $mockDoctrineObject;
    }

    protected function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();
        $this->sut = new BusReg($this->sm);

        $this->em = $this->createPartialMock(
            '\Doctrine\ORM\EntityManager',
            [
                'persist',
                'flush',
                'getUnitOfWork',
                'getClassMetadata',
                'getMetadataFactory',
                'createQueryBuilder',
                'find',
                'lock',
                'remove'
            ]
        );

        $this->sut->setEntityManager($this->em);

        $this->sm
            ->shouldReceive('get')
            ->with('Config')
            ->andReturn(
                [
                    'entity_namespaces' => [],
                ]
            );
    }
    /**
     * @group service_abstract
     */
    public function testUpdate()
    {
        $id = 7;

        $data = array(
            'version' => 1,
            'otherServices' => [
                7
            ]
        );

        $mockEntity = $this->createPartialMock(BusRegEntity::class, array('clearProperties'));
        $mockEntity->expects($this->once())->method('clearProperties');

        $mockDoctrineObject = $this->mockHydrator();
        $mockDoctrineObject->expects($this->once())
            ->method('hydrate')
            ->with($data, $this->isInstanceOf(BusRegEntity::class))
            ->will($this->returnValue($mockEntity));

        $this->em->expects($this->once())
            ->method('lock')
            ->will($this->returnValue($mockEntity));
        $this->em->expects($this->atLeastOnce())
            ->method('find')
            ->will($this->returnValue($mockEntity));

        $this->em->expects($this->once())
            ->method('persist')
            ->will($this->returnValue($mockEntity));

        $this->em->expects($this->once())
            ->method('flush');

        $mockDataService = $this->createMock('Olcs\Db\Service\BusReg\OtherServicesManager');
        $mockDataService->expects($this->once())
            ->method('processOtherServiceNumbers')
            ->with($mockEntity, [['serviceNo' => 'abc']])
            ->willReturn([7]);

        $this->sm->setService('Olcs\Db\Service\BusReg\OtherServicesManager', $mockDataService);

        $postData = array(
            'version' => 1,
            'otherServices' => [
                ['serviceNo' => 'abc']
            ]
        );

        $this->assertTrue($this->sut->update($id, $postData));
    }
}
