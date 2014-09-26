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
 *        @ORM\Index(name="IDX_C3C90DC9B93D5C07", columns={"prohibition_type"}),
 *        @ORM\Index(name="IDX_C3C90DC9DE12AB56", columns={"created_by"}),
 *        @ORM\Index(name="IDX_C3C90DC9CF10D4F5", columns={"case_id"}),
 *        @ORM\Index(name="IDX_C3C90DC965CF370E", columns={"last_modified_by"})
 *    }
 * )
 */
class Prohibition implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\CreatedByManyToOne,
        Traits\CaseManyToOneAlt1,
        Traits\LastModifiedByManyToOne,
        Traits\Vrm20Field,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Prohibition type
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="prohibition_type", referencedColumnName="id", nullable=false)
     */
    protected $prohibitionType;

    /**
     * Prohibition date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="prohibition_date", nullable=false)
     */
    protected $prohibitionDate;

    /**
     * Cleared date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="cleared_date", nullable=true)
     */
    protected $clearedDate;

    /**
     * Is trailer
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="is_trailer", nullable=false)
     */
    protected $isTrailer;

    /**
     * Imposed at
     *
     * @var string
     *
     * @ORM\Column(type="string", name="imposed_at", length=255, nullable=true)
     */
    protected $imposedAt;

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
}
