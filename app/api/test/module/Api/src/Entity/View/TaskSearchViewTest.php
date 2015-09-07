<?php

namespace Dvsa\OlcsTest\Api\Entity\View;

use Dvsa\Olcs\Api\Entity\View\TaskSearchView;

/**
 * Task Search View entity unit tests
 *
 * N.B. NOT Auto-Generated!!
 *
 * @author Dan Eggleston <dan@stolenegg.com>
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
            'id' => 69,
            'irfoOpName' => 'irfoop',
            'isClosed' => 1,
            'licenceCount' => 99,
            'licenceId' => 8,
            'licenceNo' => 'LICNO',
            'linkDisplay' => 'linky',
            'linkId' => 9,
            'linkType' => 'lt',
            'name' => 'nom',
            'opName' => 'op',
            'ownerName' => 'owner',
            'taskSubCategory' => 10,
            'taskSubCategoryName' => 'subcat',
            'taskSubType' => 'subtype',
            'teamName' => 'team',
            'transportManagerId' => 11,
            'urgent' => 1,
            'irfoOrganisationId' => 12,
            'submissionId' => 13,
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
