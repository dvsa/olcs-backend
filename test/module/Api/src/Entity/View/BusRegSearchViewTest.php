<?php

namespace Dvsa\OlcsTest\Api\Entity\View;

use Dvsa\Olcs\Api\Entity\View\BusRegSearchView;

/**
 * BusRegSearchView entity unit tests
 *
 * N.B. NOT Auto-Generated!!
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
class BusRegSearchViewTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DocumentSearchView
     */
    protected $entity;

    /**
     * @var array
     */
    protected $testData;

    public function setUp()
    {
        $this->testData = [
            'id' => 15,
            'serviceNo' => '46474',
            'regNo' => 'PD2737280/15711',
            'licId' => '110',
            'licNo' => 'PD2737280',
            'licStatus' => 'Not Yet Submitted',
            'organisationName' => 'Leeds city council',
            'startPoint' => 'Leeds',
            'finishPoint' => 'Doncaster',
            'busRegStatus' => 'Registered',
            'routeNo' => '15711',
            'variationNo' => '6'
        ];
        $this->entity = new BusRegSearchView();

        // no public methods to set data exist so we must use reflection api
        // (which, apparently, is what Doctrine does)
        $ref = new \ReflectionObject($this->entity);
        foreach (array_keys($this->testData) as $property) {
            $refProperty = $ref->getProperty($property);
            $refProperty->setAccessible(true);
            $refProperty->setValue($this->entity, $this->testData[$property]);
        }
    }

    public function testGetters()
    {
        // test all teh getters
        foreach ($this->testData as $property => $value) {
            $getter = 'get'.ucfirst($property);
            $this->assertEquals($value, $this->entity->$getter());
        }
    }
}
