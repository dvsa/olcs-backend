<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * PublicationLink Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedDate", timeAware=true)
 * @ORM\Table(name="publication_link",
 *    indexes={
 *        @ORM\Index(name="ix_publication_link_licence_id", columns={"licence_id"}),
 *        @ORM\Index(name="ix_publication_link_publication_id", columns={"publication_id"}),
 *        @ORM\Index(name="ix_publication_link_pi_id", columns={"pi_id"}),
 *        @ORM\Index(name="ix_publication_link_traffic_area_id", columns={"traffic_area_id"}),
 *        @ORM\Index(name="ix_publication_link_application_id", columns={"application_id"}),
 *        @ORM\Index(name="ix_publication_link_bus_reg_id", columns={"bus_reg_id"}),
 *        @ORM\Index(name="ix_publication_link_publication_section_id", columns={"publication_section_id"}),
 *        @ORM\Index(name="ix_publication_link_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_publication_link_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_licence_publication_transport_manager_id1_idx", columns={"transport_manager_id"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_publication_link_olbs_key_olbs_type", columns={"olbs_key","olbs_type"})
 *    }
 * )
 */
class PublicationLink implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\BusRegManyToOneAlt1,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomDeletedDateField,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\OlbsKeyField,
        Traits\OlbsType32Field,
        Traits\TrafficAreaManyToOne,
        Traits\TransportManagerManyToOne,
        Traits\CustomVersionField;

    /**
     * Application
     *
     * @var \Olcs\Db\Entity\Application
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Application", inversedBy="publicationLinks")
     * @ORM\JoinColumn(name="application_id", referencedColumnName="id", nullable=true)
     */
    protected $application;

    /**
     * Licence
     *
     * @var \Olcs\Db\Entity\Licence
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Licence", inversedBy="publicationLinks")
     * @ORM\JoinColumn(name="licence_id", referencedColumnName="id", nullable=true)
     */
    protected $licence;

    /**
     * Pi
     *
     * @var \Olcs\Db\Entity\Pi
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Pi", inversedBy="publicationLinks")
     * @ORM\JoinColumn(name="pi_id", referencedColumnName="id", nullable=true)
     */
    protected $pi;

    /**
     * Publication
     *
     * @var \Olcs\Db\Entity\Publication
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Publication", inversedBy="publicationLinks")
     * @ORM\JoinColumn(name="publication_id", referencedColumnName="id", nullable=false)
     */
    protected $publication;

    /**
     * Publication section
     *
     * @var \Olcs\Db\Entity\PublicationSection
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\PublicationSection")
     * @ORM\JoinColumn(name="publication_section_id", referencedColumnName="id", nullable=false)
     */
    protected $publicationSection;

    /**
     * Text1
     *
     * @var string
     *
     * @ORM\Column(type="text", name="text1", length=65535, nullable=true)
     */
    protected $text1;

    /**
     * Text2
     *
     * @var string
     *
     * @ORM\Column(type="text", name="text2", length=65535, nullable=true)
     */
    protected $text2;

    /**
     * Text3
     *
     * @var string
     *
     * @ORM\Column(type="text", name="text3", length=65535, nullable=true)
     */
    protected $text3;

    /**
     * Set the application
     *
     * @param \Olcs\Db\Entity\Application $application
     * @return PublicationLink
     */
    public function setApplication($application)
    {
        $this->application = $application;

        return $this;
    }

    /**
     * Get the application
     *
     * @return \Olcs\Db\Entity\Application
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * Set the licence
     *
     * @param \Olcs\Db\Entity\Licence $licence
     * @return PublicationLink
     */
    public function setLicence($licence)
    {
        $this->licence = $licence;

        return $this;
    }

    /**
     * Get the licence
     *
     * @return \Olcs\Db\Entity\Licence
     */
    public function getLicence()
    {
        return $this->licence;
    }

    /**
     * Set the pi
     *
     * @param \Olcs\Db\Entity\Pi $pi
     * @return PublicationLink
     */
    public function setPi($pi)
    {
        $this->pi = $pi;

        return $this;
    }

    /**
     * Get the pi
     *
     * @return \Olcs\Db\Entity\Pi
     */
    public function getPi()
    {
        return $this->pi;
    }

    /**
     * Set the publication
     *
     * @param \Olcs\Db\Entity\Publication $publication
     * @return PublicationLink
     */
    public function setPublication($publication)
    {
        $this->publication = $publication;

        return $this;
    }

    /**
     * Get the publication
     *
     * @return \Olcs\Db\Entity\Publication
     */
    public function getPublication()
    {
        return $this->publication;
    }

    /**
     * Set the publication section
     *
     * @param \Olcs\Db\Entity\PublicationSection $publicationSection
     * @return PublicationLink
     */
    public function setPublicationSection($publicationSection)
    {
        $this->publicationSection = $publicationSection;

        return $this;
    }

    /**
     * Get the publication section
     *
     * @return \Olcs\Db\Entity\PublicationSection
     */
    public function getPublicationSection()
    {
        return $this->publicationSection;
    }

    /**
     * Set the text1
     *
     * @param string $text1
     * @return PublicationLink
     */
    public function setText1($text1)
    {
        $this->text1 = $text1;

        return $this;
    }

    /**
     * Get the text1
     *
     * @return string
     */
    public function getText1()
    {
        return $this->text1;
    }

    /**
     * Set the text2
     *
     * @param string $text2
     * @return PublicationLink
     */
    public function setText2($text2)
    {
        $this->text2 = $text2;

        return $this;
    }

    /**
     * Get the text2
     *
     * @return string
     */
    public function getText2()
    {
        return $this->text2;
    }

    /**
     * Set the text3
     *
     * @param string $text3
     * @return PublicationLink
     */
    public function setText3($text3)
    {
        $this->text3 = $text3;

        return $this;
    }

    /**
     * Get the text3
     *
     * @return string
     */
    public function getText3()
    {
        return $this->text3;
    }
}
