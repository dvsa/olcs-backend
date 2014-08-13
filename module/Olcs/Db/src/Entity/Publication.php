<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * Publication Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="publication",
 *    indexes={
 *        @ORM\Index(name="fk_publication_traffic_area1_idx", columns={"traffic_area_id"}),
 *        @ORM\Index(name="fk_publication_ref_data1_idx", columns={"pub_status"}),
 *        @ORM\Index(name="fk_publication_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_publication_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class Publication implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\TrafficAreaManyToOne,
        Traits\PublicationNoField,
        Traits\PubType3Field,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Pub status
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="pub_status", referencedColumnName="id")
     */
    protected $pubStatus;

    /**
     * Pub date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="pub_date", nullable=true)
     */
    protected $pubDate;

    /**
     * Doc name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="doc_name", length=255, nullable=true)
     */
    protected $docName;


    /**
     * Set the pub status
     *
     * @param \Olcs\Db\Entity\RefData $pubStatus
     * @return Publication
     */
    public function setPubStatus($pubStatus)
    {
        $this->pubStatus = $pubStatus;

        return $this;
    }

    /**
     * Get the pub status
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getPubStatus()
    {
        return $this->pubStatus;
    }

    /**
     * Set the pub date
     *
     * @param \DateTime $pubDate
     * @return Publication
     */
    public function setPubDate($pubDate)
    {
        $this->pubDate = $pubDate;

        return $this;
    }

    /**
     * Get the pub date
     *
     * @return \DateTime
     */
    public function getPubDate()
    {
        return $this->pubDate;
    }

    /**
     * Set the doc name
     *
     * @param string $docName
     * @return Publication
     */
    public function setDocName($docName)
    {
        $this->docName = $docName;

        return $this;
    }

    /**
     * Get the doc name
     *
     * @return string
     */
    public function getDocName()
    {
        return $this->docName;
    }
}
