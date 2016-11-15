<?php

namespace Dvsa\OlcsTest\Api\Entity\Publication;

use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\Publication\Publication as Entity;
use Dvsa\Olcs\Api\Entity\Doc\Document as DocumentEntity;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea as TrafficAreaEntity;
use Dvsa\Olcs\Api\Entity\Doc\DocTemplate as DocTemplateEntity;
use Mockery as m;

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
     * Test creating a publication
     */
    public function testCreate()
    {
        $trafficArea = m::mock(TrafficAreaEntity::class);
        $pubStatus = m::mock(RefData::class);
        $docTemplate = m::mock(DocTemplateEntity::class);
        $pubDate = new \DateTime('2015-12-25 00:00:00'); //14 days later
        $pubType = 'A&D';
        $publicationNo = 111;

        $entity = new Entity($trafficArea, $pubStatus, $docTemplate, $pubDate, $pubType, $publicationNo);

        $this->assertEquals($trafficArea, $entity->getTrafficArea());
        $this->assertEquals($pubStatus, $entity->getPubStatus());
        $this->assertEquals($docTemplate, $entity->getDocTemplate());
        $this->assertEquals($pubDate, $entity->getPubDate());
        $this->assertEquals($pubType, $entity->getPubType());
        $this->assertEquals($publicationNo, $entity->getPublicationNo());
    }

    /**
     * Test updating published documents. Checks police document has been set, and that main document has been set as
     * read only
     */
    public function testUpdatePublishedDocuments()
    {
        $policeDocument = m::mock(DocumentEntity::class);

        $entity = $this->instantiate(Entity::class);
        $entity->updatePublishedDocuments($policeDocument);

        $this->assertEquals($policeDocument, $entity->getPoliceDocument());
    }

    /**
     * Tests getting the next publication date
     */
    public function testGetNextPublicationDate()
    {
        $pubDate = '2015-12-11';
        $newPubDate  = '2015-12-18'; // +7 days

        $entity = $this->instantiate(Entity::class);
        $entity->setPubDate($pubDate);

        $this->assertEquals($entity->getNextPublicationDate()->format('Y-m-d'), $newPubDate);
    }

    /**
     * Check exception is thrown if no pub date is set when generating the future date
     *
     * @dataProvider notGeneratedStatusProvider
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    public function testGetNextPublicationDateThrowsException()
    {
        $entity = $this->instantiate(Entity::class);
        $entity->getNextPublicationDate();
    }

    /**
     * Tests publish
     */
    public function testPublish()
    {
        $entity = $this->instantiate(Entity::class);
        $entity->setPubStatus(new RefData(Entity::PUB_GENERATED_STATUS));
        $printedStatus = new RefData(Entity::PUB_PRINTED_STATUS);

        $entity->publish($printedStatus);
        $this->assertEquals($entity::PUB_PRINTED_STATUS, $entity->getPubStatus()->getId());
    }

    /**
     * @dataProvider notGeneratedStatusProvider
     * @param string $pubStatus
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\ForbiddenException
     */
    public function testPublishThrowsException($pubStatus)
    {
        $entity = $this->instantiate(Entity::class);
        $entity->setPubStatus(new RefData($pubStatus));
        $printedStatus = new RefData(Entity::PUB_PRINTED_STATUS);

        $entity->publish($printedStatus);
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
        $entity = $this->instantiate(Entity::class);
        $entity->setPubStatus(new RefData(Entity::PUB_NEW_STATUS));
        $generateStatus = new RefData(Entity::PUB_GENERATED_STATUS);
        $document = new DocumentEntity(1);

        $entity->generate($document, $generateStatus);
        $this->assertEquals($entity::PUB_GENERATED_STATUS, $entity->getPubStatus()->getId());
    }

    /**
     * @dataProvider notNewStatusProvider
     * @param string $pubStatus
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\ForbiddenException
     */
    public function testGenerateThrowsException($pubStatus)
    {
        $entity = $this->instantiate(Entity::class);
        $entity->setPubStatus(new RefData($pubStatus));
        $generateStatus = new RefData(Entity::PUB_GENERATED_STATUS);
        $document = new DocumentEntity(1);

        $entity->generate($document, $generateStatus);
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
     * @param $pubStatus
     * @param $isNew
     */
    public function testIsNew($pubStatus, $isNew)
    {
        $entity = $this->instantiate(Entity::class);
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
