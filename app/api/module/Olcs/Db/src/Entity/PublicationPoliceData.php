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
 *        @ORM\Index(name="ix_publication_police_data_publication_link_id", columns={"publication_link_id"}),
 *        @ORM\Index(name="ix_publication_police_data_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_publication_police_data_last_modified_by", columns={"last_modified_by"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_publication_police_data_olbs_key", columns={"olbs_key"})
 *    }
 * )
 */
class PublicationPoliceData implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\BirthDateField,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\FamilyName35Field,
        Traits\Forename35Field,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\OlbsKeyField,
        Traits\CustomVersionField;

    /**
     * Olbs dob
     *
     * @var string
     *
     * @ORM\Column(type="string", name="olbs_dob", length=20, nullable=true)
     */
    protected $olbsDob;

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
}
