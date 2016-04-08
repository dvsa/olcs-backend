<?php

namespace Dvsa\Olcs\Api\Entity\Publication;

use Doctrine\ORM\Mapping as ORM;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Entity\Doc\Document as DocumentEntity;
use Dvsa\Olcs\Api\Domain\Exception\RuntimeException;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea as TrafficAreaEntity;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Entity\Doc\DocTemplate as DocTemplateEntity;

/**
 * Publication Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="publication",
 *    indexes={
 *        @ORM\Index(name="ix_publication_traffic_area_id", columns={"traffic_area_id"}),
 *        @ORM\Index(name="ix_publication_pub_status", columns={"pub_status"}),
 *        @ORM\Index(name="ix_publication_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_publication_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_publication_document1_idx", columns={"document_id"}),
 *        @ORM\Index(name="fk_publication_doc_template1_idx", columns={"doc_template_id"})
 *    }
 * )
 */
class Publication extends AbstractPublication
{
    const PUB_NEW_STATUS = 'pub_s_new';
    const PUB_GENERATED_STATUS = 'pub_s_generated';
    const PUB_PRINTED_STATUS = 'pub_s_printed';
    const PUB_TYPE_N_P = 'N&P';
    const PUB_TYPE_A_D = 'A&D';

    public function __construct(
        TrafficAreaEntity $trafficArea,
        RefData $pubStatus,
        DocTemplateEntity $docTemplate,
        $pubDate,
        $pubType,
        $publicationNo
    ) {
        $this->trafficArea = $trafficArea;
        $this->pubStatus = $pubStatus;
        $this->docTemplate = $docTemplate;
        $this->pubDate = $pubDate;
        $this->pubType = $pubType;
        $this->publicationNo = $publicationNo;
    }

    /**
     * Whether a document can be published
     *
     * @return bool
     */
    public function canPublish()
    {
        return $this->getPubStatus()->getId() === self::PUB_GENERATED_STATUS;
    }

    /**
     * Whether a document can be generated
     *
     * @return bool
     */
    public function canGenerate()
    {
        return $this->getPubStatus()->getId() === self::PUB_NEW_STATUS;
    }

    /**
     * Publish a publication providing the current status is correct
     *
     * @param RefData $newPubStatus
     * @throws ForbiddenException
     */
    public function publish(RefData $newPubStatus)
    {
        if (!$this->canPublish()) {
            throw new ForbiddenException('Only publications with status of Generated may be published');
        }

        $this->pubStatus = $newPubStatus;
    }

    /**
     * Update published documents. This is done separately from changing the publication status, as the police document
     * itself is created afterwards
     *
     * @param DocumentEntity $policeDocument
     */
    public function updatePublishedDocuments(DocumentEntity $policeDocument)
    {
        $this->document->setIsReadOnly('Y');
        $this->policeDocument = $policeDocument;
    }

    /**
     * Generate a publication providing the current status is correct
     *
     * @param DocumentEntity $document
     * @param RefData $newPubStatus
     * @throws ForbiddenException
     */
    public function generate(DocumentEntity $document, RefData $newPubStatus)
    {
        if (!$this->canGenerate()) {
            throw new ForbiddenException('Only publications with status of New may be generated');
        }

        $this->pubStatus = $newPubStatus;
        $this->document = $document;
    }

    /**
     * when we access the date we get a string, however we still want to return a \DateTime
     *
     * @return \DateTime
     * @throws RuntimeException
     */
    public function getNextPublicationDate()
    {
        $newPubDate = \DateTime::createFromFormat('Y-m-d', $this->pubDate);

        if (!$newPubDate instanceof \DateTime) {
            throw new RuntimeException('Can\'t generate future publication date without current publication date');
        }

        $newPubDate->add(new \DateInterval('P14D'));

        return $newPubDate;
    }

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->pubStatus->getId() === self::PUB_NEW_STATUS;
    }
}
