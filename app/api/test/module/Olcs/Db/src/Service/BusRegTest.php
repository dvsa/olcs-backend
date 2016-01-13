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
        $mockDoctrineObject = $this->getMock('\stdClass', ['hydrate']);

        $mockHydratorManager = $this->getMock('\stdClass', ['get']);
        $mockHydratorManager->expects($this->once())
            ->method('get')
            ->with('DoctrineModule\Stdlib\Hydrator\DoctrineObject')
            ->will($this->returnValue($mockDoctrineObject));

        $this->sm->setService('HydratorManager', $mockHydratorManager);

        return $mockDoctrineObject;
    }

    protected function setUp()
    {
        $this->sut = new BusReg();

        $this->sm = Bootstrap::getServiceManager();
        $this->em = $this->getMock(
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
            ],
            array(),
            '',
            false
        );

        $this->sut->setServiceLocator($this->sm);
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

        $mockEntity = $this->getMock(BusRegEntity::class, array('clearProperties'));
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

        $mockDataService = $this->getMock('Olcs\Db\Service\BusReg\OtherServicesManager');
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
