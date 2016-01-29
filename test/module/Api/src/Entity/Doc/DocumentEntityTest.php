<?php

namespace Dvsa\OlcsTest\Api\Entity\Doc;

use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\Doc\Document as Entity;
use Mockery as m;

/**
 * Document Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class DocumentEntityTest extends EntityTester
{
    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

    public function testUpdateDocument()
    {
        $sut = m::mock(Entity::class)->makePartial();
        $sut->updateDocument('identifier', 'description', 'filename', 1, 2, 3, 1, 1, '2015-01-01', 100);

        $this->assertEquals('identifier', $sut->getIdentifier());
        $this->assertEquals('description', $sut->getDescription());
        $this->assertEquals('filename', $sut->getFilename());
        $this->assertEquals(1, $sut->getLicence());
        $this->assertEquals(2, $sut->getCategory());
        $this->assertEquals(3, $sut->getSubCategory());
        $this->assertEquals(1, $sut->getIsExternal());
        $this->assertEquals(1, $sut->getIsReadOnly());
        $this->assertEquals('2015-01-01', $sut->getIssuedDate());
        $this->assertEquals(100, $sut->getSize());
    }
}
