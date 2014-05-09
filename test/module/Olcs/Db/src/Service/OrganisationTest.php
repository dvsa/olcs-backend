<?php

/**
 * Tests Organisation Service
 *
 * @author Jakub Igla <jakub.igla@valtech.co.uk>
 */

namespace OlcsTest\Db\Service;

use PHPUnit_Framework_TestCase;

/**
 * Tests Organisation Service
 *
 * @author Jakub Igla <jakub.igla@valtech.co.uk>
 */
class OrganisationTest extends PHPUnit_Framework_TestCase
{
    
    /**
     * Setup the service
     */
    protected function setUp()
    {
        $this->service = $this->getMock('\Olcs\Db\Service\Organisation', array(
        	'log',
            'getEntityManager',
            'extract',
            'getDoctrineHydrator', 
            'dbPersist', 
            'dbFlush',
            'getBundleCreator',
        ));
    }

    /**
     * Test getValidSearchFields
     */
    public function testGetValidSearchFields()
    {
        $expected = array();

        $this->assertEquals($expected, $this->service->getValidSearchFields());
    }
    
    /**
     * Test get
     *
     * @group Service
     */
    public function testGet()
    {
        $id = 7;
        $data = array(
                'foo' => 'bar'
        );
    
        $orgEntity = $this->getMock('\stdClass');
        $licenceEntity = $this->getMock('\stdClass', array('getOrganisation'));
        $licenceEntity->expects($this->once())
            ->method('getOrganisation')
            ->will($this->returnValue($orgEntity));
    
        $this->service->expects($this->once())
            ->method('extract')
            ->with($orgEntity)
            ->will($this->returnValue($data));
    
        $this->service->expects($this->once())
            ->method('log');
    
        $repoMock = $this->getMock('\stdClass', array('findOneBy'));
        $repoMock->expects($this->once())
            ->method('findOneBy')
            ->with(array('id' => $id))
            ->will($this->returnValue($licenceEntity));
        
        $entityManagerMock = $this->getMock('\stdClass', array('getRepository'));
        $entityManagerMock->expects($this->once())
            ->method('getRepository')
            ->will($this->returnValue($repoMock));
        
        $this->service->expects($this->once())
            ->method('getEntityManager')
            ->will($this->returnValue($entityManagerMock));
        
    
        $this->assertEquals($data, $this->service->getByLicenceId($id));
    }
    
    /**
     * Test get where is no licence entity
     *
     * @group Service
     */
    public function testGetNoLicenceEntity()
    {
        $id = 7;
        $data = array(
                'foo' => 'bar'
        );
    
        $this->service->expects($this->once())
        ->method('log');
    
        $repoMock = $this->getMock('\stdClass', array('findOneBy'));
        $repoMock->expects($this->once())
        ->method('findOneBy')
        ->with(array('id' => $id))
        ->will($this->returnValue(null));
    
        $entityManagerMock = $this->getMock('\stdClass', array('getRepository'));
        $entityManagerMock->expects($this->once())
        ->method('getRepository')
        ->will($this->returnValue($repoMock));
    
        $this->service->expects($this->once())
        ->method('getEntityManager')
        ->will($this->returnValue($entityManagerMock));
    
    
        $this->assertEquals(null, $this->service->getByLicenceId($id));
    }
    
    /**
     * Test get
     *
     * @group Service
     */
    public function testGetNoOrgEntity()
    {
        $id = 7;
        $data = array(
                'foo' => 'bar'
        );

        $licenceEntity = $this->getMock('\stdClass', array('getOrganisation'));
        $licenceEntity->expects($this->once())
            ->method('getOrganisation')
            ->will($this->returnValue(null));
    
        $this->service->expects($this->once())
            ->method('log');
    
        $repoMock = $this->getMock('\stdClass', array('findOneBy'));
        $repoMock->expects($this->once())
            ->method('findOneBy')
            ->with(array('id' => $id))
            ->will($this->returnValue($licenceEntity));
    
        $entityManagerMock = $this->getMock('\stdClass', array('getRepository'));
        $entityManagerMock->expects($this->once())
            ->method('getRepository')
            ->will($this->returnValue($repoMock));
    
        $this->service->expects($this->once())
            ->method('getEntityManager')
            ->will($this->returnValue($entityManagerMock));
    
    
        $this->assertEquals(null, $this->service->getByLicenceId($id));
    }
    
    /**
     * Test Update
     *  Without version
     *
     * @expectedException \Olcs\Db\Exceptions\NoVersionException
     *
     * @group Service
     * @group ServiceAbstract
     */
    public function testUpdateWithoutVersion()
    {
        $id = 7;
        $data = array(
        );
    
        $this->service->expects($this->once())
            ->method('log');
    
        $this->service->updateByLicenceId($id, $data);
    }
    
    /**
     * Test get where is no licence entity
     *
     * @group Service
     */
    public function testUpdateNoLicenceEntity()
    {
        $id = 7;
        $data = array(
                'foo' => 'bar',
                'version' => 1,
        );
    
        $this->service->expects($this->once())
            ->method('log');
    
        $repoMock = $this->getMock('\stdClass', array('findOneBy'));
        $repoMock->expects($this->once())
            ->method('findOneBy')
            ->with(array('id' => $id))
            ->will($this->returnValue(null));
    
        $entityManagerMock = $this->getMock('\stdClass', array('getRepository'));
        $entityManagerMock->expects($this->once())
            ->method('getRepository')
            ->will($this->returnValue($repoMock));
    
        $this->service->expects($this->once())
            ->method('getEntityManager')
            ->will($this->returnValue($entityManagerMock));
    
    
        $this->assertEquals(null, $this->service->updateByLicenceId($id, $data));
    }
    
    /**
     * Test get
     *
     * @group Service
     */
    public function testUpdateNoOrgEntity()
    {
        $id = 7;
        $data = array(
                'foo' => 'bar',
                'version' => 1,
        );
    
        $licenceEntity = $this->getMock('\stdClass', array('getOrganisation'));
        $licenceEntity->expects($this->once())
            ->method('getOrganisation')
            ->will($this->returnValue(null));
    
        $this->service->expects($this->once())
            ->method('log');
    
        $repoMock = $this->getMock('\stdClass', array('findOneBy'));
        $repoMock->expects($this->once())
            ->method('findOneBy')
            ->with(array('id' => $id))
            ->will($this->returnValue($licenceEntity));
    
        $entityManagerMock = $this->getMock('\stdClass', array('getRepository'));
        $entityManagerMock->expects($this->once())
            ->method('getRepository')
            ->will($this->returnValue($repoMock));
    
        $this->service->expects($this->once())
            ->method('getEntityManager')
            ->will($this->returnValue($entityManagerMock));
    
    
        $this->assertEquals(null, $this->service->updateByLicenceId($id, $data));
    }
    
    /**
     * Test get
     *
     * @group Service
     */
    public function testUpdate()
    {
        $id = 7;
        $data = array(
                'foo' => 'bar',
                'version' => 1,
        );
        
        $orgEntity = $this->getMock('\stdClass');
        
        $licenceEntity = $this->getMock('\stdClass', array('getOrganisation'));
        $licenceEntity->expects($this->once())
            ->method('getOrganisation')
            ->will($this->returnValue($orgEntity));
        
        $mockHydrator = $this->getMock('\stdClass', array('hydrate'));
        $mockHydrator->expects($this->once())
            ->method('hydrate')
            ->with($data, $orgEntity)
            ->will($this->returnValue($orgEntity));
        
        $repoMock = $this->getMock('\stdClass', array('findOneBy'));
        $repoMock->expects($this->once())
            ->method('findOneBy')
            ->with(array('id' => $id))
            ->will($this->returnValue($licenceEntity));
        
        $mockEntityManager = $this->getMock('\stdClass', array('lock', 'getRepository'));
        $mockEntityManager->expects($this->once())
            ->method('getRepository')
            ->will($this->returnValue($repoMock));
        
        $mockEntityManager->expects($this->once())
            ->method('lock')
            ->will($this->returnValue($orgEntity));
    
        
    
        $this->service->expects($this->once())
            ->method('log');
    
        
    
        
        $this->service->expects($this->once())
            ->method('getDoctrineHydrator')
            ->will($this->returnValue($mockHydrator));
        
        $this->service->expects($this->exactly(2))
            ->method('getEntityManager')
            ->will($this->returnValue($mockEntityManager));
        
        $this->service->expects($this->once())
            ->method('dbPersist')
            ->will($this->returnValue($orgEntity));
        
        $this->service->expects($this->once())
            ->method('dbFlush');
    
        $this->assertEquals(true, $this->service->updateByLicenceId($id, $data));
    }

    public function testGetApplicationsList()
    {
        $data = array('organisation' => 1);
        $results = array(
            array('id' => 1),
        );
        $return = array(
            'Count' => count($results),
            'Results' => $results,
        );

        $sqlMethods = array('select', 'from', 'innerJoin', 'add', 'setParameter');
        $mockQb = $this->getMock('\stdClass', array_merge($sqlMethods, array('getQuery')));

        foreach($sqlMethods as $method){
            $mockQb->expects($this->any())
                ->method($method)
                ->will($this->returnValue($mockQb))
            ;
        }

        $mockQuery = $this->getMock('\stdClass', array('getResult'));
        $mockQuery->expects($this->once())
            ->method('getResult')
            ->will($this->returnValue($results))
        ;

        $mockQb->expects($this->any())
            ->method('getQuery')
            ->will($this->returnValue($mockQuery))
        ;



        $mockEntityManager = $this->getMockBuilder('\Doctrine\ORM\EntityManager', array('createQueryBuilder'))->disableOriginalConstructor()->getMock();
        $mockEntityManager->expects($this->any())
            ->method('createQueryBuilder')
            ->will($this->returnValue($mockQb));

        $this->service->expects($this->once())
            ->method('getEntityManager')
            ->will($this->returnValue($mockEntityManager))
        ;

        $mockBundleCreator = $this->getMock('\stdClass', array('buildEntityBundle'));
        $mockBundleCreator->expects($this->once())
            ->method('buildEntityBundle')
            ->will($this->returnValue(array('id' => 1)));

        $this->service->expects($this->once())
            ->method('getBundleCreator')
            ->will($this->returnValue($mockBundleCreator));

        $this->assertEquals($return, $this->service->getApplicationsList($data));
    }
    
}
