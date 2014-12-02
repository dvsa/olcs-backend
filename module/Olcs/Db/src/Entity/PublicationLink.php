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
 *        @ORM\Index(name="fk_publication_has_licence_licence1_idx", columns={"licence_id"}),
 *        @ORM\Index(name="fk_publication_has_licence_publication1_idx", columns={"publication_id"}),
 *        @ORM\Index(name="fk_licence_publication_pi_detail1_idx", columns={"pi_id"}),
 *        @ORM\Index(name="fk_licence_publication_traffic_area1_idx", columns={"traffic_area_id"}),
 *        @ORM\Index(name="fk_licence_publication_application1_idx", columns={"application_id"}),
 *        @ORM\Index(name="fk_licence_publication_bus_reg1_idx", columns={"bus_reg_id"}),
 *        @ORM\Index(name="fk_licence_publication_publication_section1_idx", columns={"publication_section_id"}),
 *        @ORM\Index(name="fk_licence_publication_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_licence_publication_user2_idx", columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_licence_publication_tm_pi_hearing1_idx", columns={"tm_pi_hearing_id"})
 *    }
 * )
 */
class PublicationLink implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\CreatedByManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\BusRegManyToOneAlt1,
        Traits\ApplicationManyToOne,
        Traits\LicenceManyToOneAlt1,
        Traits\TrafficAreaManyToOne,
        Traits\PublicationNoField,
        Traits\PubType3Field,
        Traits\CustomDeletedDateField,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Publication section
     *
     * @var \Olcs\Db\Entity\PublicationSection
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\PublicationSection", fetch="LAZY")
     * @ORM\JoinColumn(name="publication_section_id", referencedColumnName="id", nullable=false)
     */
    protected $publicationSection;

    /**
     * Tm pi hearing
     *
     * @var \Olcs\Db\Entity\TmPiHearing
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\TmPiHearing", fetch="LAZY")
     * @ORM\JoinColumn(name="tm_pi_hearing_id", referencedColumnName="id", nullable=true)
     */
    protected $tmPiHearing;

    /**
     * Pi
     *
     * @var \Olcs\Db\Entity\Pi
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Pi", fetch="LAZY")
     * @ORM\JoinColumn(name="pi_id", referencedColumnName="id", nullable=true)
     */
    protected $pi;

    /**
     * Publication
     *
     * @var \Olcs\Db\Entity\Publication
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Publication", fetch="LAZY")
     * @ORM\JoinColumn(name="publication_id", referencedColumnName="id", nullable=false)
     */
    protected $publication;

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
     * Orig pub date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="orig_pub_date", nullable=true)
     */
    protected $origPubDate;

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
     * Set the tm pi hearing
     *
     * @param \Olcs\Db\Entity\TmPiHearing $tmPiHearing
     * @return PublicationLink
     */
    public function setTmPiHearing($tmPiHearing)
    {
        $this->tmPiHearing = $tmPiHearing;

        return $this;
    }

    /**
     * Get the tm pi hearing
     *
     * @return \Olcs\Db\Entity\TmPiHearing
     */
    public function getTmPiHearing()
    {
        return $this->tmPiHearing;
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

    /**
     * Set the orig pub date
     *
     * @param \DateTime $origPubDate
     * @return PublicationLink
     */
    public function setOrigPubDate($origPubDate)
    {
        $this->origPubDate = $origPubDate;

        return $this;
    }

    /**
     * Get the orig pub date
     *
     * @return \DateTime
     */
    public function getOrigPubDate()
    {
        return $this->origPubDate;
    }
}
