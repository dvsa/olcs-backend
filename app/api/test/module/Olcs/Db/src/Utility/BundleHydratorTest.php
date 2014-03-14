<?php

/**
 * Tests the Bundle Hydrator
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace OlcsTest\Db\Utility;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
use Olcs\Db\Utility\BundleHydrator;

/**
 * Tests the Bundle Hydrator
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class BundleHydratorTest extends AbstractHttpControllerTestCase
{
    private $doctrineObject;

    public function setUp()
    {
        $this->doctrineObject = $this->getMockBuilder('\DoctrineModule\Stdlib\Hydrator\DoctrineObject')->disableOriginalConstructor()->getMock();
    }

    /**
     * Tests the getNestedEntityFromEntities method
     */
    public function testGetNestedEntityFromEntities()
    {
        $bundleHydrator = new BundleHydrator($this->doctrineObject);

        $json = '{"User\/1":{"username":"Bobby","password":"password","displayName":"BobbyTest","__REFS":{"roles":["Role\/1"]}},"Role\/1":{"name":"Test Role","handle":"testrole","__REFS":{"permissions":["Permission\/1","Permission\/2"]}},"Permission\/1":{"name":"Test Permission 1","handle":"testpermission1"},"Permission\/2":{"name":"Test Permission 2","handle":"testpermission2"}}';

        $response = $bundleHydrator->getNestedEntityFromEntities(json_decode($json, true));

        $this->assertTrue($response instanceof \OlcsEntities\Entity\User);

        $this->assertEquals('Bobby', $response->getUsername());
        $this->assertEquals('password', $response->getPassword());
        $this->assertEquals('BobbyTest', $response->getDisplayName());
    }
}
