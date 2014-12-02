<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * PublicationPoliceData Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="publication_police_data",
 *    indexes={
 *        @ORM\Index(name="fk_publication_police_data_publication_link1_idx", columns={"publication_link_id"}),
 *        @ORM\Index(name="fk_publication_police_data_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_publication_police_data_user2_idx", columns={"last_modified_by"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_olbs_id", columns={"olbs_id"})
 *    }
 * )
 */
class PublicationPoliceData implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\Forename35Field,
        Traits\FamilyName35Field,
        Traits\BirthDateField,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Publication link
     *
     * @var \Olcs\Db\Entity\PublicationLink
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\PublicationLink")
     * @ORM\JoinColumn(name="publication_link_id", referencedColumnName="id", nullable=false)
     */
    protected $publicationLink;

    /**
     * Olbs dob
     *
     * @var string
     *
     * @ORM\Column(type="string", name="olbs_dob", length=20, nullable=true)
     */
    protected $olbsDob;

    /**
     * Olbs id
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="olbs_id", nullable=true)
     */
    protected $olbsId;

    /**
     * Set the publication link
     *
     * @param \Olcs\Db\Entity\PublicationLink $publicationLink
     * @return PublicationPoliceData
     */
    public function setPublicationLink($publicationLink)
    {
        $this->publicationLink = $publicationLink;

        return $this;
    }

    /**
     * Get the publication link
     *
     * @return \Olcs\Db\Entity\PublicationLink
     */
    public function getPublicationLink()
    {
        return $this->publicationLink;
    }

    /**
     * Set the olbs dob
     *
     * @param string $olbsDob
     * @return PublicationPoliceData
     */
    public function setOlbsDob($olbsDob)
    {
        $this->olbsDob = $olbsDob;

        return $this;
    }

    /**
     * Get the olbs dob
     *
     * @return string
     */
    public function getOlbsDob()
    {
        return $this->olbsDob;
    }

    /**
     * Set the olbs id
     *
     * @param int $olbsId
     * @return PublicationPoliceData
     */
    public function setOlbsId($olbsId)
    {
        $this->olbsId = $olbsId;

        return $this;
    }

    /**
     * Get the olbs id
     *
     * @return int
     */
    public function getOlbsId()
    {
        return $this->olbsId;
    }
}
