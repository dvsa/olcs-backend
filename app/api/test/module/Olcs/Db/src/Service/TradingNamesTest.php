<?php

/**
 * Tests TradingNames Service
 *
 * @author Jakub Igla <jakub.igla@valtech.co.uk>
 */

namespace OlcsTest\Db\Service;

use PHPUnit_Framework_TestCase;

/**
 * Tests TradingNames Service
 *
 * @author Jakub Igla <jakub.igla@valtech.co.uk>
 */
class TradingNamesTest extends PHPUnit_Framework_TestCase
{
    /**
     * Setup the service
     *
     * @return void
     */
    protected function setUp()
    {
        $this->service = $this->getMock(
            '\Olcs\Db\Service\TradingName', array(
                'getEntityManager',
            )
        );
    }

    /**
     * Test getValidSearchFields
     *
     * @return void
     */
    public function testRemove()
    {
        $q = $this->getMock('\stdClass', array('execute'));

        $mockEM = $this->getMock('\stdClass', array('createQuery'));
        $mockEM->expects($this->once())
            ->method('createQuery')
            ->will($this->returnValue($q));

        $this->service->expects($this->once())
            ->method('getEntityManager')
            ->will($this->returnValue($mockEM));

        $this->service->removeAll(1);
    }

}
