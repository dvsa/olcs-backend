<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
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
 *        @ORM\Index(name="ix_publication_traffic_area_id", columns={"traffic_area_id"}),
 *        @ORM\Index(name="ix_publication_pub_status", columns={"pub_status"}),
 *        @ORM\Index(name="ix_publication_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_publication_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_publication_document_id", columns={"document_id"}),
 *        @ORM\Index(name="ix_publication_doc_template_id", columns={"doc_template_id"})
 *    }
 * )
 */
class Publication implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\DocumentManyToOneAlt1,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\TrafficAreaManyToOne,
        Traits\CustomVersionField;

    /**
     * Doc name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="doc_name", length=255, nullable=true)
     */
    protected $docName;

    /**
     * Doc template
     *
     * @var \Olcs\Db\Entity\DocTemplate
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\DocTemplate")
     * @ORM\JoinColumn(name="doc_template_id", referencedColumnName="id", nullable=false)
     */
    protected $docTemplate;

    /**
     * Pub date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="pub_date", nullable=true)
     */
    protected $pubDate;

    /**
     * Pub status
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="pub_status", referencedColumnName="id", nullable=false)
     */
    protected $pubStatus;

    /**
     * Pub type
     *
     * @var string
     *
     * @ORM\Column(type="string", name="pub_type", length=3, nullable=false)
     */
    protected $pubType;

    /**
     * Publication no
     *
     * @var int
     *
     * @ORM\Column(type="smallint", name="publication_no", nullable=false)
     */
    protected $publicationNo;

    /**
     * Publication link
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\PublicationLink", mappedBy="publication")
     */
    protected $publicationLinks;

    /**
     * Initialise the collections
     */
    public function __construct()
    {
        $this->publicationLinks = new ArrayCollection();
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

    /**
     * Set the doc template
     *
     * @param \Olcs\Db\Entity\DocTemplate $docTemplate
     * @return Publication
     */
    public function setDocTemplate($docTemplate)
    {
        $this->docTemplate = $docTemplate;

        return $this;
    }

    /**
     * Get the doc template
     *
     * @return \Olcs\Db\Entity\DocTemplate
     */
    public function getDocTemplate()
    {
        return $this->docTemplate;
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
     * Set the pub type
     *
     * @param string $pubType
     * @return Publication
     */
    public function setPubType($pubType)
    {
        $this->pubType = $pubType;

        return $this;
    }

    /**
     * Get the pub type
     *
     * @return string
     */
    public function getPubType()
    {
        return $this->pubType;
    }

    /**
     * Set the publication no
     *
     * @param int $publicationNo
     * @return Publication
     */
    public function setPublicationNo($publicationNo)
    {
        $this->publicationNo = $publicationNo;

        return $this;
    }

    /**
     * Get the publication no
     *
     * @return int
     */
    public function getPublicationNo()
    {
        return $this->publicationNo;
    }

    /**
     * Set the publication link
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $publicationLinks
     * @return Publication
     */
    public function setPublicationLinks($publicationLinks)
    {
        $this->publicationLinks = $publicationLinks;

        return $this;
    }

    /**
     * Get the publication links
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getPublicationLinks()
    {
        return $this->publicationLinks;
    }

    /**
     * Add a publication links
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $publicationLinks
     * @return Publication
     */
    public function addPublicationLinks($publicationLinks)
    {
        if ($publicationLinks instanceof ArrayCollection) {
            $this->publicationLinks = new ArrayCollection(
                array_merge(
                    $this->publicationLinks->toArray(),
                    $publicationLinks->toArray()
                )
            );
        } elseif (!$this->publicationLinks->contains($publicationLinks)) {
            $this->publicationLinks->add($publicationLinks);
        }

        return $this;
    }

    /**
     * Remove a publication links
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $publicationLinks
     * @return Publication
     */
    public function removePublicationLinks($publicationLinks)
    {
        if ($this->publicationLinks->contains($publicationLinks)) {
            $this->publicationLinks->removeElement($publicationLinks);
        }

        return $this;
    }
}
