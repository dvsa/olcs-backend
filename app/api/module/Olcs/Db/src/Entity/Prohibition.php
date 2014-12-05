<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * Prohibition Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="prohibition",
 *    indexes={
 *        @ORM\Index(name="fk_prohibition_case1_idx", columns={"case_id"}),
 *        @ORM\Index(name="fk_prohibition_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_prohibition_user2_idx", columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_prohibition_ref_data1_idx", columns={"prohibition_type"})
 *    }
 * )
 */
class Prohibition implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField,
        Traits\Vrm20Field;

    /**
     * Case
     *
     * @var \Olcs\Db\Entity\Cases
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Cases", inversedBy="prohibitions")
     * @ORM\JoinColumn(name="case_id", referencedColumnName="id", nullable=false)
     */
    protected $case;

    /**
     * Cleared date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="cleared_date", nullable=true)
     */
    protected $clearedDate;

    /**
     * Imposed at
     *
     * @var string
     *
     * @ORM\Column(type="string", name="imposed_at", length=255, nullable=true)
     */
    protected $imposedAt;

    /**
     * Is trailer
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="is_trailer", nullable=false)
     */
    protected $isTrailer = 0;

    /**
     * Prohibition date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="prohibition_date", nullable=false)
     */
    protected $prohibitionDate;

    /**
     * Prohibition type
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="prohibition_type", referencedColumnName="id", nullable=false)
     */
    protected $prohibitionType;

    /**
     * Set the case
     *
     * @param \Olcs\Db\Entity\Cases $case
     * @return Prohibition
     */
    public function setCase($case)
    {
        $this->case = $case;

        return $this;
    }

    /**
     * Get the case
     *
     * @return \Olcs\Db\Entity\Cases
     */
    public function getCase()
    {
        return $this->case;
    }

    /**
     * Set the cleared date
     *
     * @param \DateTime $clearedDate
     * @return Prohibition
     */
    public function setClearedDate($clearedDate)
    {
        $this->clearedDate = $clearedDate;

        return $this;
    }

    /**
     * Get the cleared date
     *
     * @return \DateTime
     */
    public function getClearedDate()
    {
        return $this->clearedDate;
    }

    /**
     * Set the imposed at
     *
     * @param string $imposedAt
     * @return Prohibition
     */
    public function setImposedAt($imposedAt)
    {
        $this->imposedAt = $imposedAt;

        return $this;
    }

    /**
     * Get the imposed at
     *
     * @return string
     */
    public function getImposedAt()
    {
        return $this->imposedAt;
    }

    /**
     * Set the is trailer
     *
     * @param string $isTrailer
     * @return Prohibition
     */
    public function setIsTrailer($isTrailer)
    {
        $this->isTrailer = $isTrailer;

        return $this;
    }

    /**
     * Get the is trailer
     *
     * @return string
     */
    public function getIsTrailer()
    {
        return $this->isTrailer;
    }

    /**
     * Set the prohibition date
     *
     * @param \DateTime $prohibitionDate
     * @return Prohibition
     */
    public function setProhibitionDate($prohibitionDate)
    {
        $this->prohibitionDate = $prohibitionDate;

        return $this;
    }

    /**
     * Get the prohibition date
     *
     * @return \DateTime
     */
    public function getProhibitionDate()
    {
        return $this->prohibitionDate;
    }

    /**
     * Set the prohibition type
     *
     * @param \Olcs\Db\Entity\RefData $prohibitionType
     * @return Prohibition
     */
    public function setProhibitionType($prohibitionType)
    {
        $this->prohibitionType = $prohibitionType;

        return $this;
    }

    /**
     * Get the prohibition type
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getProhibitionType()
    {
        return $this->prohibitionType;
    }
}
