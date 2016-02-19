<?php

namespace Dvsa\OlcsTest\Api\Entity\View;

use Dvsa\Olcs\Api\Entity\View\ViVhlView;

/**
 * VI Vehicle View entity unit tests
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class ViVhlViewTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ViVhlView
     */
    protected $entity;

    /**
     * @var array
     */
    protected $testData;

    public function setUp()
    {
        $this->testData = [
            'id' => 1,
            'licId' => 2,
            'vhlId' => 3,
            'viLine' => 'vil'
        ];

        $this->entity = new ViVhlView();

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
        foreach ($this->testData as $property => $value) {
            $getter = 'get'.ucfirst($property);
            $this->assertEquals($value, $this->entity->$getter());
        }
    }
}
