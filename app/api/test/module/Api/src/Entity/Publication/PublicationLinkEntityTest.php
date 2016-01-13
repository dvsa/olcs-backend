<?php

namespace Dvsa\OlcsTest\Api\Entity\Publication;

use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\Publication\PublicationLink as Entity;
use Dvsa\Olcs\Api\Entity\Publication\Publication as PublicationEntity;
use Mockery as m;

/**
 * PublicationLink Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class PublicationLinkEntityTest extends EntityTester
{
    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

    /**
     * Tests updateText throws an exception if the corresponding publication isn't of status New
     *
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\ForbiddenException
     */
    public function testUpdateTextThrowsException()
    {
        $publicationMock = m::mock(PublicationEntity::class);
        $publicationMock->shouldReceive('isNew')->andReturn(false);

        $entity = new Entity();
        $entity->setPublication($publicationMock);
        $entity->updateText('', '', '');
    }

    public function testUpdateText()
    {
        $publicationMock = m::mock(PublicationEntity::class);
        $publicationMock->shouldReceive('isNew')->andReturn(true);

        $entity = new Entity();
        $entity->setPublication($publicationMock);

        $text1 = 'text1';
        $text2 = 'text2';
        $text3 = 'text3';

        $entity->updateText($text1, $text2, $text3);

        $this->assertEquals($text1, $entity->getText1());
        $this->assertEquals($text2, $entity->getText2());
        $this->assertEquals($text3, $entity->getText3());
    }
}
