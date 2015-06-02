<?php


namespace OlcsTest\Db\Service;

use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Mockery as m;
use Olcs\Db\Service\Organisation;
use OlcsTest\Bootstrap;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation as OrganisationEntity;

/**
 * Class OrganisationTest
 * @package OlcsTest\Db\Service
 */
class OrganisationTest extends TestCase
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
        $this->sut = new Organisation();

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

        $data = array(
            'version' => 1,
            'irfoPartners' => [
                7
            ]
        );

        $mockEntity = $this->getMock(OrganisationEntity::class, array('clearProperties'));
        $mockEntity->expects($this->once())->method('clearProperties');

        $mockDoctrineObject = $this->mockHydrator();
        $mockDoctrineObject->expects($this->once())
            ->method('hydrate')
            ->with($data, $this->isInstanceOf(OrganisationEntity::class))
            ->will($this->returnValue($mockEntity));

        $this->em->expects($this->once())
            ->method('lock')
            ->will($this->returnValue($mockEntity));
        $this->em->expects($this->atLeastOnce())
            ->method('find')
            ->will($this->returnValue($mockEntity));

        $this->mockLogger->expects($this->once())->method('info');

        $this->em->expects($this->once())
            ->method('persist')
            ->will($this->returnValue($mockEntity));

        $this->em->expects($this->once())
            ->method('flush');

        $mockDataService = $this->getMock('Olcs\Db\Service\Organisation\IrfoPartnersManager');
        $mockDataService->expects($this->once())
            ->method('processIrfoPartners')
            ->with($mockEntity, [['name' => 'abc']])
            ->willReturn([7]);

        $this->sm->setService('Olcs\Db\Service\Organisation\IrfoPartnersManager', $mockDataService);

        $postData = array(
            'version' => 1,
            'irfoPartners' => [
                ['name' => 'abc']
            ]
        );

        $this->assertTrue($this->sut->update($id, $postData));
    }
}
