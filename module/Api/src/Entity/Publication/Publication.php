<?php

namespace Dvsa\Olcs\Api\Entity\Publication;

use Doctrine\ORM\Mapping as ORM;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Entity\Doc\Document as DocumentEntity;
use Dvsa\Olcs\Api\Domain\Exception\RuntimeException;

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

    /**
     * Publish a publication providing the current status is correct
     *
     * @throws ForbiddenException
     */
    public function publish()
    {
        if ($this->getPubStatus()->getId() !== self::PUB_GENERATED_STATUS) {
            throw new ForbiddenException('Only publications with status of Generated may be published');
        }

        $this->pubStatus = new RefData(self::PUB_PRINTED_STATUS);
    }

    /**
     * Generate a publication providing the current status is correct
     *
     * @param DocumentEntity $document
     * @throws ForbiddenException
     */
    public function generate(DocumentEntity $document)
    {
        if ($this->getPubStatus()->getId() !== self::PUB_NEW_STATUS) {
            throw new ForbiddenException('Only publications with status of New may be generated');
        }

        $this->pubStatus = new RefData(self::PUB_GENERATED_STATUS);
        $this->document = $document;
    }

    /**
     * @return \DateTime
     * @throws RuntimeException
     */
    public function getNextPublicationDate()
    {
        if (!$this->pubDate instanceof \DateTime) {
            throw new RuntimeException('Can\'t generate future publication date without current publication date');
        }

        $newPubDate = clone $this->pubDate;
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
