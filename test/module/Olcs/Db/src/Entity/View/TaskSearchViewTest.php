<?php

namespace OlcsTest\Db\Entity\View;

use Olcs\Db\Entity\View\TaskSearchView;

/**
 * Task Search View entity unit tests
 *
 * N.B. NOT Auto-Generated!!
 */
class TaskSearchViewTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TaskSearchView
     */
    protected $entity;

    /**
     * @var array
     */
    protected $testData;

    public function setUp()
    {
        parent::setUp();
        $this->testData = [
            'actionDate' => '2014-11-26',
            'applicationId' => 2,
            'assignedToTeam' => 3,
            'assignedToUser' => 4,
            'busRegId' => 5,
            'caseId' => 6,
            'category' => 7,
            'categoryName' => 'cat',
            'description' => 'desc',
            'familyName' => 'fam',
            'irfoOpName' => 'irfoop',
            'isClosed' => 1,
            'licenceId' => 8,
            'licenceNo' => 'LICNO',
            'licenceCount' => 99,
            'linkDisplay' => 'linky',
            'linkId' => 9,
            'linkType' => 'lt',
            'name' => 'nom',
            'opName' => 'op',
            'taskSubCategory' => 10,
            'taskSubCategoryName' => 'subcat',
            'taskSubType' => 'subtype',
            'transportManagerId' => 11,
            'urgent' => 1,
            'ownerName' => 'owner',
        ];

        $this->entity = new TaskSearchView();

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

    public function testDefaultValues()
    {
        $entity = new TaskSearchView();
        $this->assertEquals(0, $entity->getIsClosed());
        $this->assertEquals(0, $entity->getUrgent());
    }
}
