<?php

/**
 * Tests ApplicationOperatingCentre Service
 *
 * @author Jess Rowbottom <jess.rowbottom@valtech.co.uk>
 */

namespace OlcsTest\Db\Service;

use PHPUnit_Framework_TestCase;
use Olcs\Db\Service\ApplicationOperatingCentre;

/**
 * Tests ApplicationOperatingCentre Service
 *
 * @author Jess Rowbottom <jess.rowbottom@valtech.co.uk>
 */
class ApplicationOperatingCentreTest extends PHPUnit_Framework_TestCase
{
    /**
     * Setup the service
     */
    protected function setUp()
    {
        $this->service = $this->getMock('\Olcs\Db\Service\ApplicationOperatingCentre', array('log'));
    }

    /**
     * Test getByApplicationId
     */
    public function testGetByApplicationId()
    {
        $options = array(
            'applicationId' => '10'
        );

        $this->service->expects($this->any())
            ->method('log')
            ->will($this->returnValue(true));

        $mockQuery = $this->getMock('\stdClass', array('fetchAll', 'execute', 'bindValue'));
        $mockQuery->expects($this->any())
            ->method('fetchAll')
            ->will($this->returnValue(array(array('foo' => 'bar'))));

        $mockQuery->expects($this->any())
            ->method('execute');

        $mockConnection = $this->getMock('\stdClass', array('prepare'));
        $mockConnection->expects($this->any())
            ->method('prepare')
            ->will($this->returnValue($mockQuery));

        $mockEntityManager = $this->getMockBuilder('\Doctrine\ORM\EntityManager', array('getConnection'))->disableOriginalConstructor()->getMock();
        $mockEntityManager->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue($mockConnection));
        $this->service->setEntityManager($mockEntityManager);

        $result = $this->service->getByApplicationId($options);

        $this->assertEquals(array(array('foo' => 'bar')), $result);

    }

}
