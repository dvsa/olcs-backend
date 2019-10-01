<?php

namespace Dvsa\OlcsTest\Api\Entity\View;

use Dvsa\Olcs\Api\Entity\View\IrhpApplicationView;

/**
 * IrhpApplicationView
 */
class IrhpApplicationViewTest extends \PHPUnit\Framework\TestCase
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
            'licenceId' => 10,
            'organisationId' => 20,
            'licNo' => 'ABC123',
            'applicationRef' => 'ABC123 / 15',
            'permitsRequired' => 100,
            'typeId' => 1,
            'typeDescription' => 'Type description',
            'statusId' => 'STATUS_ID',
            'statusDescription' => 'Status description',
            'dateReceived' => '2015-01-01',
            'stockValidTo' => '2019-12-31',
            'periodNameKey' => 'i.am.a.key'
        ];
        $this->entity = new IrhpApplicationView();

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
