<?php

namespace Dvsa\OlcsTest\Api\Entity\Publication;

use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\Publication\Publication as Entity;
use Dvsa\Olcs\Api\Entity\Doc\Document as DocumentEntity;

/**
 * Publication Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class PublicationEntityTest extends EntityTester
{
    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

    /**
     * Tests getting the next publication date
     */
    public function testGetNextPublicationDate()
    {
        $pubDate = new \DateTime();

        $entity = new Entity();
        $entity->setPubDate($pubDate);

        $newPubDate = $entity->getNextPublicationDate();

        $this->assertEquals($pubDate->add(new \DateInterval('P14D')), $newPubDate);
    }

    /**
     * Check exception is thrown if no pub date is set when generating the future date
     *
     * @dataProvider notGeneratedStatusProvider
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    public function testGetNextPublicationDateThrowsException()
    {
        $entity = new Entity();
        $entity->getNextPublicationDate();
    }

    /**
     * Tests publish
     */
    public function testPublish()
    {
        $entity = new Entity();
        $entity->setPubStatus(new RefData(Entity::PUB_GENERATED_STATUS));

        $entity->publish();
        $this->assertEquals($entity::PUB_PRINTED_STATUS, $entity->getPubStatus()->getId());
    }

    /**
     * @dataProvider notGeneratedStatusProvider
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\ForbiddenException
     */
    public function testPublishThrowsException($pubStatus)
    {
        $entity = new Entity();
        $entity->setPubStatus(new RefData($pubStatus));

        $entity->publish();
    }

    /**
     * Provider of publication statuses that aren't generated
     *
     * @return array
     */
    public function notGeneratedStatusProvider()
    {
        return [
            [Entity::PUB_NEW_STATUS],
            [Entity::PUB_PRINTED_STATUS]
        ];
    }

    /**
     * Tests generate
     */
    public function testGenerate()
    {
        $entity = new Entity();
        $entity->setPubStatus(new RefData(Entity::PUB_NEW_STATUS));
        $document = new DocumentEntity(1);

        $entity->generate($document);
        $this->assertEquals($entity::PUB_GENERATED_STATUS, $entity->getPubStatus()->getId());
    }

    /**
     * @dataProvider notNewStatusProvider
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\ForbiddenException
     */
    public function testGenerateThrowsException($pubStatus)
    {
        $entity = new Entity();
        $entity->setPubStatus(new RefData($pubStatus));
        $document = new DocumentEntity(1);

        $entity->generate($document);
    }

    /**
     * Provider of publication statuses that aren't new
     *
     * @return array
     */
    public function notNewStatusProvider()
    {
        return [
            [Entity::PUB_GENERATED_STATUS],
            [Entity::PUB_PRINTED_STATUS]
        ];
    }

    /**
     * Test that isNew function only returns new if the status is new
     *
     * @dataProvider isNewPublicationStatusProvider
     */
    public function testIsNew($pubStatus, $isNew)
    {
        $entity = new Entity();
        $entity->setPubStatus(new RefData($pubStatus));

        $this->assertEquals($isNew, $entity->isNew());
    }

    /**
     * Provides statuses and whether isNew should return true or false
     *
     * @return array
     */
    public function isNewPublicationStatusProvider()
    {
        return [
            [Entity::PUB_NEW_STATUS, true],
            [Entity::PUB_GENERATED_STATUS, false],
            [Entity::PUB_PRINTED_STATUS, false]
        ];
    }
}
