<?php

/**
 * Tests Licence Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace OlcsTest\Db\Service;

use PHPUnit_Framework_TestCase;
use Olcs\Db\Service\Licence;

/**
 * Tests Licence Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceTest extends PHPUnit_Framework_TestCase
{
    /**
     * Setup the service
     */
    protected function setUp()
    {
        // We may want to inject the ServiceLocator in the future
        $this->service = new Licence();
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
     * Test findLicences
     *  without pagination stuff
     */
    public function testFindLicencesWithoutPagination()
    {
        $options = array(
            'operatorName' => 'Bob',
            'entityType' => 'SomeType',
            'licNo' => 'ABC',
            'postcode' => 'AB1 ',
            'address' => '123 Street ',
            'town' => 'DEF',
            'operatorId' => 7
        );

        $expected = array(
            ':operatorName' => '%Bob%',
            ':entityType' => 'SomeType',
            ':licNo' => '%ABC%',
            ':postcode' => '%AB1 %',
            ':address' => '%123 Street %',
            ':town' => '%DEF%',
            ':operatorId' => '%7%'
        );

        $mockQuery = $this->getMock('\stdClass', array('fetchAll', 'execute'));

        $mockQuery->expects($this->at(1))
            ->method('fetchAll')
            ->will($this->returnValue(array(array('resultCount' => 1))));

        $mockQuery->expects($this->at(3))
            ->method('fetchAll')
            ->will($this->returnValue(array(array('foo' => 'bar'))));

        $mockQuery->expects($this->any())
            ->method('execute')
            ->with($expected);

        $mockConnection = $this->getMock('\stdClass', array('prepare'));

        $mockConnection->expects($this->any())
            ->method('prepare')
            ->will($this->returnValue($mockQuery));

        $mockEntityManager = $this->getMockBuilder('\Doctrine\ORM\EntityManager', array('getConnection'))
            ->disableOriginalConstructor()->getMock();

        $mockEntityManager->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue($mockConnection));

        $this->service->setEntityManager($mockEntityManager);

        $result = $this->service->findLicences($options);

        $this->assertEquals(array(array('resultCount' => 1), array(array('foo' => 'bar'))), $result);
    }

    /**
     * Test findLicences
     *  with pagination stuff
     */
    public function testFindLicencesWithPagination()
    {
        $options = array(
            'operatorName' => 'Bob',
            'entityType' => 'SomeType',
            'licNo' => 'ABC',
            'postcode' => 'AB1 ',
            'address' => '123 Street ',
            'town' => 'DEF',
            'operatorId' => 7,
            'limit' => 20,
            'page' => 2,
            'sort' => 'correspondenceAddress'
        );

        $expected = array(
            ':operatorName' => '%Bob%',
            ':entityType' => 'SomeType',
            ':licNo' => '%ABC%',
            ':postcode' => '%AB1 %',
            ':address' => '%123 Street %',
            ':town' => '%DEF%',
            ':operatorId' => '%7%'
        );

        $mockQuery = $this->getMock('\stdClass', array('fetchAll', 'execute'));

        $mockQuery->expects($this->at(1))
            ->method('fetchAll')
            ->will($this->returnValue(array(array('resultCount' => 1))));

        $mockQuery->expects($this->at(3))
            ->method('fetchAll')
            ->will($this->returnValue(array(array('foo' => 'bar'))));

        $mockQuery->expects($this->any())
            ->method('execute')
            ->with($expected);

        $mockConnection = $this->getMock('\stdClass', array('prepare'));

        $mockConnection->expects($this->any())
            ->method('prepare')
            ->will($this->returnValue($mockQuery));

        $mockEntityManager = $this->getMockBuilder('\Doctrine\ORM\EntityManager', array('getConnection'))
            ->disableOriginalConstructor()->getMock();

        $mockEntityManager->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue($mockConnection));

        $this->service->setEntityManager($mockEntityManager);

        $result = $this->service->findLicences($options);

        $this->assertEquals(array(array('resultCount' => 1), array(array('foo' => 'bar'))), $result);
    }

    /**
     * Test findLicences
     *  without options
     */
    public function testFindLicencesWithoutOptions()
    {
        $options = array(
        );

        $expected = array(
        );

        $mockQuery = $this->getMock('\stdClass', array('fetchAll', 'execute'));

        $mockQuery->expects($this->at(1))
            ->method('fetchAll')
            ->will($this->returnValue(array(array('resultCount' => 1))));

        $mockQuery->expects($this->at(3))
            ->method('fetchAll')
            ->will($this->returnValue(array(array('foo' => 'bar'))));

        $mockQuery->expects($this->any())
            ->method('execute')
            ->with($expected);

        $mockConnection = $this->getMock('\stdClass', array('prepare'));

        $mockConnection->expects($this->any())
            ->method('prepare')
            ->will($this->returnValue($mockQuery));

        $mockEntityManager = $this->getMockBuilder('\Doctrine\ORM\EntityManager', array('getConnection'))
            ->disableOriginalConstructor()->getMock();

        $mockEntityManager->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue($mockConnection));

        $this->service->setEntityManager($mockEntityManager);

        $result = $this->service->findLicences($options);

        $this->assertEquals(array(array('resultCount' => 1), array(array('foo' => 'bar'))), $result);
    }

    /**
     * Test findLicences
     *  with null pagination
     */
    public function testFindLicencesWithNullPagination()
    {
        $options = array(
            'limit' => 0,
            'sort' => ''
        );

        $expected = array(
        );

        $mockQuery = $this->getMock('\stdClass', array('fetchAll', 'execute'));

        $mockQuery->expects($this->at(1))
            ->method('fetchAll')
            ->will($this->returnValue(array(array('resultCount' => 1))));

        $mockQuery->expects($this->at(3))
            ->method('fetchAll')
            ->will($this->returnValue(array(array('foo' => 'bar'))));

        $mockQuery->expects($this->any())
            ->method('execute')
            ->with($expected);

        $mockConnection = $this->getMock('\stdClass', array('prepare'));

        $mockConnection->expects($this->any())
            ->method('prepare')
            ->will($this->returnValue($mockQuery));

        $mockEntityManager = $this->getMockBuilder('\Doctrine\ORM\EntityManager', array('getConnection'))
            ->disableOriginalConstructor()->getMock();

        $mockEntityManager->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue($mockConnection));

        $this->service->setEntityManager($mockEntityManager);

        $result = $this->service->findLicences($options);

        $this->assertEquals(array(array('resultCount' => 1), array(array('foo' => 'bar'))), $result);
    }

    /**
     * Test findPersonsAndLicences
     *  without pagination stuff
     */
    public function testFindPersonsAndLicencesWithoutPagination()
    {
        $options = array(
            'firstName' => 'Bob',
            'lastName' => 'John',
            'dateOfBirth' => '01-01-',
        );

        $expected = array(
            ':firstName' => '%Bob%',
            ':lastName' => '%John%',
            ':dateOfBirth' => '01-01-%',
        );

        $mockQuery = $this->getMock('\stdClass', array('fetchAll', 'execute'));

        $mockQuery->expects($this->at(1))
            ->method('fetchAll')
            ->will($this->returnValue(array(array('resultCount' => 1))));

        $mockQuery->expects($this->at(3))
            ->method('fetchAll')
            ->will($this->returnValue(array(array('foo' => 'bar'))));

        $mockQuery->expects($this->any())
            ->method('execute')
            ->with($expected);

        $mockConnection = $this->getMock('\stdClass', array('prepare'));

        $mockConnection->expects($this->any())
            ->method('prepare')
            ->will($this->returnValue($mockQuery));

        $mockEntityManager = $this->getMockBuilder('\Doctrine\ORM\EntityManager', array('getConnection'))
            ->disableOriginalConstructor()->getMock();

        $mockEntityManager->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue($mockConnection));

        $this->service->setEntityManager($mockEntityManager);

        $result = $this->service->findPersonsAndLicences($options);

        $this->assertEquals(array(array('resultCount' => 1), array(array('foo' => 'bar'))), $result);
    }

    /**
     * Test findPersonsAndLicences
     *  with pagination stuff
     */
    public function testFindPersonsAndLicencesWithPagination()
    {
        $options = array(
            'firstName' => 'Bob',
            'lastName' => 'John',
            'dateOfBirth' => '01-01-',
            'limit' => 20,
            'page' => 2,
            'sort' => 'firstName'
        );

        $expected = array(
            ':firstName' => '%Bob%',
            ':lastName' => '%John%',
            ':dateOfBirth' => '01-01-%',
        );

        $mockQuery = $this->getMock('\stdClass', array('fetchAll', 'execute'));

        $mockQuery->expects($this->at(1))
            ->method('fetchAll')
            ->will($this->returnValue(array(array('resultCount' => 1))));

        $mockQuery->expects($this->at(3))
            ->method('fetchAll')
            ->will($this->returnValue(array(array('foo' => 'bar'))));

        $mockQuery->expects($this->any())
            ->method('execute')
            ->with($expected);

        $mockConnection = $this->getMock('\stdClass', array('prepare'));

        $mockConnection->expects($this->any())
            ->method('prepare')
            ->will($this->returnValue($mockQuery));

        $mockEntityManager = $this->getMockBuilder('\Doctrine\ORM\EntityManager', array('getConnection'))
            ->disableOriginalConstructor()->getMock();

        $mockEntityManager->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue($mockConnection));

        $this->service->setEntityManager($mockEntityManager);

        $result = $this->service->findPersonsAndLicences($options);

        $this->assertEquals(array(array('resultCount' => 1), array(array('foo' => 'bar'))), $result);
    }

    /**
     * Test findPersonsAndLicences
     *  without options
     */
    public function testFindPersonsAndLicencesWithoutOptions()
    {
        $options = array(
        );

        $expected = array(
        );

        $mockQuery = $this->getMock('\stdClass', array('fetchAll', 'execute'));

        $mockQuery->expects($this->at(1))
            ->method('fetchAll')
            ->will($this->returnValue(array(array('resultCount' => 1))));

        $mockQuery->expects($this->at(3))
            ->method('fetchAll')
            ->will($this->returnValue(array(array('foo' => 'bar'))));

        $mockQuery->expects($this->any())
            ->method('execute')
            ->with($expected);

        $mockConnection = $this->getMock('\stdClass', array('prepare'));

        $mockConnection->expects($this->any())
            ->method('prepare')
            ->will($this->returnValue($mockQuery));

        $mockEntityManager = $this->getMockBuilder('\Doctrine\ORM\EntityManager', array('getConnection'))
            ->disableOriginalConstructor()->getMock();

        $mockEntityManager->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue($mockConnection));

        $this->service->setEntityManager($mockEntityManager);

        $result = $this->service->findPersonsAndLicences($options);

        $this->assertEquals(array(array('resultCount' => 1), array(array('foo' => 'bar'))), $result);
    }

    /**
     * Test findPersonsAndLicences
     *  with null pagination
     */
    public function testFindPersonsAndLicencesWithNullPagination()
    {
        $options = array(
            'limit' => 0,
            'sort' => ''
        );

        $expected = array(
        );

        $mockQuery = $this->getMock('\stdClass', array('fetchAll', 'execute'));

        $mockQuery->expects($this->at(1))
            ->method('fetchAll')
            ->will($this->returnValue(array(array('resultCount' => 1))));

        $mockQuery->expects($this->at(3))
            ->method('fetchAll')
            ->will($this->returnValue(array(array('foo' => 'bar'))));

        $mockQuery->expects($this->any())
            ->method('execute')
            ->with($expected);

        $mockConnection = $this->getMock('\stdClass', array('prepare'));

        $mockConnection->expects($this->any())
            ->method('prepare')
            ->will($this->returnValue($mockQuery));

        $mockEntityManager = $this->getMockBuilder('\Doctrine\ORM\EntityManager', array('getConnection'))
            ->disableOriginalConstructor()->getMock();

        $mockEntityManager->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue($mockConnection));

        $this->service->setEntityManager($mockEntityManager);

        $result = $this->service->findPersonsAndLicences($options);

        $this->assertEquals(array(array('resultCount' => 1), array(array('foo' => 'bar'))), $result);
    }

    /**
     * Test findAllPersons
     *  without pagination stuff
     */
    public function testFindAllPersonsWithoutPagination()
    {
        $options = array(
            'firstName' => 'Bob',
            'lastName' => 'John',
            'dateOfBirth' => '01-01-1960',
        );

        $expected = array(
            ':firstName' => '%Bob%',
            ':lastName' => '%John%',
            ':dateOfBirth' => '01-01-1960',
        );

        $mockQuery = $this->getMock('\stdClass', array('fetchAll', 'execute'));

        $mockQuery->expects($this->any())
            ->method('fetchAll')
            ->will($this->returnValue(array('foo' => 'bar')));

        $mockQuery->expects($this->any())
            ->method('execute')
            ->with($expected);

        $mockConnection = $this->getMock('\stdClass', array('prepare'));

        $mockConnection->expects($this->any())
            ->method('prepare')
            ->will($this->returnValue($mockQuery));

        $mockEntityManager = $this->getMockBuilder('\Doctrine\ORM\EntityManager', array('getConnection'))
            ->disableOriginalConstructor()->getMock();

        $mockEntityManager->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue($mockConnection));

        $this->service->setEntityManager($mockEntityManager);

        $result = $this->service->findAllPersons($options);

        $this->assertEquals(array('foo' => 'bar'), $result);
    }

    /**
     * Test findAllPersons
     *  with pagination stuff
     */
    public function testFindAllPersonsWithPagination()
    {
        $options = array(
            'firstName' => 'Bob',
            'lastName' => 'John',
            'dateOfBirth' => '01-01-1960',
            'limit' => 20,
            'page' => 2,
            'sort' => 'name'
        );

        $expected = array(
            ':firstName' => '%Bob%',
            ':lastName' => '%John%',
            ':dateOfBirth' => '01-01-1960',
        );

        $mockQuery = $this->getMock('\stdClass', array('fetchAll', 'execute'));

        $mockQuery->expects($this->any())
            ->method('fetchAll')
            ->will($this->returnValue(array('foo' => 'bar')));

        $mockQuery->expects($this->any())
            ->method('execute')
            ->with($expected);

        $mockConnection = $this->getMock('\stdClass', array('prepare'));

        $mockConnection->expects($this->any())
            ->method('prepare')
            ->will($this->returnValue($mockQuery));

        $mockEntityManager = $this->getMockBuilder('\Doctrine\ORM\EntityManager', array('getConnection'))
            ->disableOriginalConstructor()->getMock();

        $mockEntityManager->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue($mockConnection));

        $this->service->setEntityManager($mockEntityManager);

        $result = $this->service->findAllPersons($options);

        $this->assertEquals(array('foo' => 'bar'), $result);
    }

    /**
     * Test findAllPersons
     *  without options
     */
    public function testFindAllPersonsWithoutOptions()
    {
        $options = array(
        );

        $expected = array(
        );

        $mockQuery = $this->getMock('\stdClass', array('fetchAll', 'execute'));

        $mockQuery->expects($this->any())
            ->method('fetchAll')
            ->will($this->returnValue(array('foo' => 'bar')));

        $mockQuery->expects($this->any())
            ->method('execute')
            ->with($expected);

        $mockConnection = $this->getMock('\stdClass', array('prepare'));

        $mockConnection->expects($this->any())
            ->method('prepare')
            ->will($this->returnValue($mockQuery));

        $mockEntityManager = $this->getMockBuilder('\Doctrine\ORM\EntityManager', array('getConnection'))
            ->disableOriginalConstructor()->getMock();

        $mockEntityManager->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue($mockConnection));

        $this->service->setEntityManager($mockEntityManager);

        $result = $this->service->findAllPersons($options);

        $this->assertEquals(array('foo' => 'bar'), $result);
    }

    /**
     * Test findAllPersons
     *  with null pagination
     */
    public function testFindAllPersonsWithNullPagination()
    {
        $options = array(
            'limit' => 0,
            'sort' => ''
        );

        $expected = array(
        );

        $mockQuery = $this->getMock('\stdClass', array('fetchAll', 'execute'));

        $mockQuery->expects($this->any())
            ->method('fetchAll')
            ->will($this->returnValue(array('foo' => 'bar')));

        $mockQuery->expects($this->any())
            ->method('execute')
            ->with($expected);

        $mockConnection = $this->getMock('\stdClass', array('prepare'));

        $mockConnection->expects($this->any())
            ->method('prepare')
            ->will($this->returnValue($mockQuery));

        $mockEntityManager = $this->getMockBuilder('\Doctrine\ORM\EntityManager', array('getConnection'))
            ->disableOriginalConstructor()->getMock();

        $mockEntityManager->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue($mockConnection));

        $this->service->setEntityManager($mockEntityManager);

        $result = $this->service->findAllPersons($options);

        $this->assertEquals(array('foo' => 'bar'), $result);
    }
}
