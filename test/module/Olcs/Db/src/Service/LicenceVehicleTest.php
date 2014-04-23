<<<<<<< HEAD
<?php

/**
 * Tests LicenceVehicle Service
 *
 * @author S Lizzio <shaun.lizzio@valtech.co.uk>
 */

namespace OlcsTest\Db\Service;

use PHPUnit_Framework_TestCase;
use Olcs\Db\Service\LicenceVehicle;

/**
 * Tests LicenceVehicle Service
 *
 * @author S Lizzio <shaun.lizzio@valtech.co.uk>
 */
class LicenceVehicleTest extends PHPUnit_Framework_TestCase
{
    /**
     * Setup the service
     */
    protected function setUp()
    {
        // We may want to inject the ServiceLocator in the future
        //$this->service = new LicenceVehicle();
        $this->service = $this->getMock(
            '\Olcs\Db\Service\LicenceVehicle',
            // Mocked methods
            [
            'log',
            'pickValidKeys',
            'getEntityManager', 
            'getEntityName', 
            'getDoctrineHydrator',
            'canSoftDelete', 
            'extractResultsArray'
            ]
        );
    }

    /**
     * Test getValidSearchFields
     */
    public function testGetValidSearchFields()
    {
        $expected = array('licence');

        $this->assertEquals($expected, $this->service->getValidSearchFields());
    }

     /**
     * Test geVehicleList
     *  Empty results
     *
     * @group Service
     * @group LicenceVehicle
     */
    public function testGetVehicleListEmptyResults()
    {

        $queryResults = array(
        );
        $results = array();
        $expected = array(
            'Count' => 0,
            'Results' => []
        );
        
        $licence_id = '99999999';
        
        $searchData = [ 
                        'controller' => 'licence-vehicle',
                        'licence' => $licence_id
                      ];
            
        $validSearchFields = ['licence'];
        
        $searchableFields = ['licence' => $licence_id];
        
        $mockEntity = $this->getMock('\stdClass', array(), array(), 'MockEntity');
        $mockEntityName = get_class($mockEntity);

        $mockQuery = $this->getMock('\stdClass', array('getResult'));
        $mockQuery->expects($this->once())
            ->method('getResult')
            ->will($this->returnValue($results));

        $mockQueryBuilder = $this->getMock('\stdClass', array('select', 'from', 'where', 'setParameters', 'getQuery'));

        $mockQueryBuilder->expects($this->once())
            ->method('select');

        $mockQueryBuilder->expects($this->once())
            ->method('from')
            ->with('MockEntity');

        $mockQueryBuilder->expects($this->once())
            ->method('where')
            ->with('a.licence = :licence');

        $mockQueryBuilder->expects($this->once())
            ->method('setParameters')
            ->with($searchableFields);

        $mockQueryBuilder->expects($this->once())
            ->method('getQuery')
            ->will($this->returnValue($mockQuery));

        $mockEntityManager = $this->getMock('\stdClass', array('createQueryBuilder'));

        $mockEntityManager->expects($this->once())
            ->method('createQueryBuilder')
            ->will($this->returnValue($mockQueryBuilder));

        
        $this->service->expects($this->once())
            ->method('log');
        
        $this->service->expects($this->once())
            ->method('pickValidKeys')
            ->with($searchData, $validSearchFields)
            ->will($this->returnValue($searchableFields));

        $this->service->expects($this->once())
            ->method('getEntityManager')
            ->will($this->returnValue($mockEntityManager));

        $this->service->expects($this->once())
            ->method('getEntityName')
            ->will($this->returnValue($mockEntityName));

        $this->service->expects($this->once())
            ->method('canSoftDelete')
            ->will($this->returnValue(false));

        $this->service->expects($this->once())
            ->method('extractResultsArray')
            ->with($queryResults)
            ->will($this->returnValue($results));
                        
        $this->assertEquals($expected, $this->service->getVehicleList($searchData));
    }

    /**
     * Test getVehicleList
     *
     * @group Service
     * @group ServiceAbstract
     */
    public function testGetVehicleList()
    {
        $queryResults = array(
            'a', 'b', 'c'
        );
        $results = array('a', 'b', 'c');
        $expected = array(
            'Count' => 3,
            'Results' => ['a', 'b', 'c']
        );
        
        $licence_id = '7';
        
        $searchData = [ 
                        'controller' => 'licence-vehicle',
                        'licence' => $licence_id
                      ];
            
        $row = ['foo' => 'bar'];
        
        $validSearchFields = ['licence'];
        
        $searchableFields = ['licence' => $licence_id];
        
        $mockEntity = $this->getMock('\stdClass', array('getVehicle'), array(), 'LicenceVehicle');
        $mockEntityName = get_class($mockEntity);
        
        $mockQuery = $this->getMock('\stdClass', array('getResult'));
        $mockQuery->expects($this->once())
            ->method('getResult')
            ->will($this->returnValue($results));

        $mockQueryBuilder = $this->getMock('\stdClass', array('select', 'from', 'where', 'setParameters', 'getQuery'));

        $mockQueryBuilder->expects($this->once())
            ->method('select');

        $mockQueryBuilder->expects($this->once())
            ->method('from')
            ->with('LicenceVehicle');

        $mockQueryBuilder->expects($this->once())
            ->method('where')
            ->with('a.licence = :licence');

        $mockQueryBuilder->expects($this->once())
            ->method('setParameters')
            ->with($searchableFields);

        $mockQueryBuilder->expects($this->once())
            ->method('getQuery')
            ->will($this->returnValue($mockQuery));

        $mockEntityManager = $this->getMock('\stdClass', array('createQueryBuilder'));

        $mockEntityManager->expects($this->once())
            ->method('createQueryBuilder')
            ->will($this->returnValue($mockQueryBuilder));
        
        $this->service->expects($this->once())
            ->method('log');
        
        $this->service->expects($this->once())
            ->method('pickValidKeys')
            ->with($searchData, $validSearchFields)
            ->will($this->returnValue($searchableFields));

        $this->service->expects($this->once())
            ->method('getEntityManager')
            ->will($this->returnValue($mockEntityManager));

        $this->service->expects($this->once())
            ->method('getEntityName')
            ->will($this->returnValue($mockEntityName));

        $this->service->expects($this->once())
            ->method('canSoftDelete')
            ->will($this->returnValue(false));
             
        $this->service->expects($this->once())
            ->method('extractResultsArray')
            ->with($queryResults)
            ->will($this->returnValue($results));
        
        $this->assertEquals($expected, $this->service->getVehicleList($searchData));
    }   
    
 }
