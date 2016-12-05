<?php

namespace Dvsa\OlcsTest\Api\Entity\View;

use Dvsa\Olcs\Api\Entity\View\BusRegBrowseView;

/**
 * BusRegBrowseView
 */
class BusRegBrowseViewTest extends \PHPUnit_Framework_TestCase
{
    protected $entity;

    /**
     * @var array
     */
    protected $testData;

    public function setUp()
    {
        $this->testData = [
            'id' => 15,
            'trafficAreaId' => 'C',
            'trafficAreaName' => 'TA name',
            'name' => 'Name',
            'address' => 'Address',
            'licNo' => 'lic no',
            'licStatus' => 'lic status',
            'regNo' => 'reg no',
            'brStatus' => 'bus reg status',
            'variationNo' => 1,
            'receivedDate' => '2015-01-01',
            'effectiveDate' => '2015-01-02',
            'endDate' => '2015-01-03',
            'serviceNo' => 'service no',
            'otherDetails' => 'other details',
            'acceptedDate' => '2015-01-04',
            'eventDescription' => 'event description',
            'eventRegistrationStatus' => 'event registration status',
        ];
        $this->entity = new BusRegBrowseView();

        $ref = new \ReflectionObject($this->entity);
        foreach (array_keys($this->testData) as $property) {
            $refProperty = $ref->getProperty($property);
            $refProperty->setAccessible(true);
            $refProperty->setValue($this->entity, $this->testData[$property]);
        }
    }

    public function testGetters()
    {
        foreach ($this->testData as $property => $value) {
            $getter = 'get'.ucfirst($property);
            $this->assertEquals($value, $this->entity->$getter());
        }
    }
}
