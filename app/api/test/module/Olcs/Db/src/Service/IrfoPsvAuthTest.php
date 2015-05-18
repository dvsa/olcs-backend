<?php


namespace OlcsTest\Db\Service;

use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Olcs\Db\Service\IrfoPsvAuth;
use OlcsTest\Bootstrap;

/**
 * Class IrfoPsvAuthTest
 * @package OlcsTest\Db\Service
 */
class IrfoPsvAuthTest extends TestCase
{
    protected $sut;

    protected $sm;

    protected $em;

    protected $mockLogger;

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
        $this->sut = new IrfoPsvAuth();

        $this->mockLogger = $this->getMock('\Zend\Log\Logger', ['info']);
        $this->sm = Bootstrap::getServiceManager();
        $this->sm->setAllowOverride(true);
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

        $this->sut->setLogger($this->mockLogger);
        $this->sut->setServiceLocator($this->sm);
        $this->sut->setEntityManager($this->em);
    }
    /**
     * @group service_abstract
     */
    public function testUpdate()
    {
        $id = 7;
        $irfoPsvAuthTypeId = 10;
        $sectionCode = '12A';

        $data = array(
            'version' => 1,
            'irfoPsvAuthType' => $irfoPsvAuthTypeId,
            'irfoFileNo' => '12A/7',
            'irfoPsvAuthNumbers' => [
                7
            ]
        );

        $mockEntity = $this->getMock('\Olcs\Db\Entity\IrfoPsvAuth', array('clearProperties'));
        $mockEntity->expects($this->once())->method('clearProperties');

        $mockIrfoPsvAuthTypeEntity = $this->getMock('\Olcs\Db\Entity\IrfoPsvAuthType', array('getSectionCode'));
        $mockIrfoPsvAuthTypeEntity->expects($this->once())->method('getSectionCode')
            ->will($this->returnValue($sectionCode));

        $findValues = [
            '\Olcs\Db\Entity\IrfoPsvAuth' => $mockEntity,
            '\Olcs\Db\Entity\IrfoPsvAuthType' => $mockIrfoPsvAuthTypeEntity,
        ];

        $mockDoctrineObject = $this->mockHydrator();
        $mockDoctrineObject->expects($this->once())
            ->method('hydrate')
            ->with($data, $this->isInstanceOf('\Olcs\Db\Entity\IrfoPsvAuth'))
            ->will($this->returnValue($mockEntity));

        $this->em->expects($this->once())
            ->method('lock')
            ->will($this->returnValue($mockEntity));
        $this->em->expects($this->any())
            ->method('find')
            ->will(
                $this->returnCallback(
                    function ($key) use ($findValues) {
                        return $findValues[$key];
                    }
                )
            );

        $this->mockLogger->expects($this->once())->method('info');

        $this->em->expects($this->once())
            ->method('persist')
            ->will($this->returnValue($mockEntity));

        $this->em->expects($this->once())
            ->method('flush');

        $mockDataService = $this->getMock('Olcs\Db\Service\IrfoPsvAuth\IrfoPsvAuthNumbersManager');
        $mockDataService->expects($this->once())
            ->method('processIrfoPsvAuthNumbers')
            ->with($mockEntity, [['name' => 'auth number']])
            ->willReturn([7]);

        $this->sm->setService('Olcs\Db\Service\IrfoPsvAuth\IrfoPsvAuthNumbersManager', $mockDataService);

        $postData = array(
            'version' => 1,
            'irfoPsvAuthType' => $irfoPsvAuthTypeId,
            'irfoPsvAuthNumbers' => [
                ['name' => 'auth number']
            ]
        );

        $this->assertTrue($this->sut->update($id, $postData));
    }
}
