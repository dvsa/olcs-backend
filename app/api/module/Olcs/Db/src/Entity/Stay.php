<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * Stay Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="stay",
 *    indexes={
 *        @ORM\Index(name="fk_stay_case1_idx", columns={"case_id"}),
 *        @ORM\Index(name="fk_stay_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_stay_user2_idx", columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_stay_ref_data1_idx", columns={"outcome"})
 *    }
 * )
 */
class Stay implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\OutcomeManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\CaseManyToOneAlt1,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Is tc
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_tc", nullable=false)
     */
    protected $isTc;

    /**
     * Request date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="request_date", nullable=true)
     */
    protected $requestDate;

    /**
     * Notes
     *
     * @var string
     *
     * @ORM\Column(type="string", name="notes", length=1024, nullable=true)
     */
    protected $notes;


    /**
     * Set the is tc
     *
     * @param string $isTc
     * @return Stay
     */
    public function setIsTc($isTc)
    {
        $this->isTc = $isTc;

        return $this;
    }

    /**
     * Get the is tc
     *
     * @return string
     */
    public function getIsTc()
    {
        return $this->isTc;
    }

    /**
     * Set the request date
     *
     * @param \DateTime $requestDate
     * @return Stay
     */
    public function setRequestDate($requestDate)
    {
        $this->requestDate = $requestDate;

        return $this;
    }

    /**
     * Get the request date
     *
     * @return \DateTime
     */
    public function getRequestDate()
    {
        return $this->requestDate;
    }

    /**
     * Set the notes
     *
     * @param string $notes
     * @return Stay
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;

        return $this;
    }

    /**
     * Get the notes
     *
     * @return string
     */
    public function getNotes()
    {
        return $this->notes;
    }
}
