<?php

namespace OlcsTest\Db\Entity\View;

use Olcs\Db\Entity\View\DocumentSearchView;

/**
 * Document Search View entity unit tests
 *
 * N.B. NOT Auto-Generated!!
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class DocumentSearchViewTest extends \PHPUnit_Framework_TestCase
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
        parent::setUp();
        $this->testData = [
            'issuedDate' => '2014-12-10',
            'category' => 1,
            'documentSubCategory' => 2,
            'description' => 'foo',
            'documentStoreIdentifier' => 'someid',
            'document' => 3,
            'categoryName' => 'cat',
            'documentSubCategoryName' => 'subcat',
            'filename' => 'file1',
            'isExternal' => 1,
            'identifier' => 'ident',
            'licenceNo' => 'LIC1',
            'licenceId' => 4,
            'familyName' => 'Smith',
            'caseId' => 5,
            'busRegId' => 6,
            'tmId' => 7,
            'ciId' => 8,
        ];
        $this->entity = new DocumentSearchView();

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
