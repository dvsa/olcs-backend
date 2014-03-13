<?php

/**
 * Tests the Bundle Helper
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Olcs\Db\Test\Helpers;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
use Olcs\Db\Helpers\BundleHelper;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;

/**
 * Tests the Bundle Helper
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class BundleHelperTest extends AbstractHttpControllerTestCase
{
    /**
     * Tests the getNestedEntityFromEntities method
     */
    public function testGetNestedEntityFromEntities()
    {
        $bundleHelper = new BundleHelper();

        $json = '{"User\/1":{"username":"Bobby","password":"password","displayName":"BobbyTest","__REFS":{"roles":["Role\/1"]}},"Role\/1":{"name":"Test Role","handle":"testrole","__REFS":{"permissions":["Permission\/1","Permission\/2"]}},"Permission\/1":{"name":"Test Permission 1","handle":"testpermission1"},"Permission\/2":{"name":"Test Permission 2","handle":"testpermission2"}}';

        $response = $bundleHelper->getNestedEntityFromEntities(json_decode($json, true));

        $this->assertTrue($response instanceof \OlcsEntities\Entity\User);

        $this->assertEquals('Bobby', $response->getUsername());
        $this->assertEquals('password', $response->getPassword());
        $this->assertEquals('BobbyTest', $response->getDisplayName());
    }

    /**
     * Tests the getTopLevelEntitiesFromNestedEntity method
     */
    public function testGetTopLevelEntitiesFromNestedEntity()
    {
        $bundleHelper = new BundleHelper();

        $json = '{"User\/1":{"username":"Bobby","password":"password","displayName":"BobbyTest","__REFS":{"roles":["Role\/1"]}},"Role\/1":{"name":"Test Role","handle":"testrole","__REFS":{"permissions":["Permission\/1","Permission\/2"]}},"Permission\/1":{"name":"Test Permission 1","handle":"testpermission1"},"Permission\/2":{"name":"Test Permission 2","handle":"testpermission2"}}';

        $response = $bundleHelper->getNestedEntityFromEntities(json_decode($json, true));

        $this->assertTrue($response instanceof \OlcsEntities\Entity\User);

        $hydrator = new DoctrineObject();

        $response = $hydrator->extract($response);

        var_dump($response);
    }
}
