<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * PsvDisc Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="psv_disc",
 *    indexes={
 *        @ORM\Index(name="ix_psv_disc_licence_id", columns={"licence_id"}),
 *        @ORM\Index(name="ix_psv_disc_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_psv_disc_last_modified_by", columns={"last_modified_by"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_psv_disc_olbs_key", columns={"olbs_key"})
 *    }
 * )
 */
class PsvDisc implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CeasedDateField,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\DiscNo50Field,
        Traits\IdIdentity,
        Traits\IsPrintingField,
        Traits\IssuedDateField,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\OlbsKeyField,
        Traits\CustomVersionField;

    /**
     * Is copy
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="is_copy", nullable=false, options={"default": 0})
     */
    protected $isCopy = 0;

    /**
     * Licence
     *
     * @var \Olcs\Db\Entity\Licence
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Licence", inversedBy="psvDiscs")
     * @ORM\JoinColumn(name="licence_id", referencedColumnName="id", nullable=false)
     */
    protected $licence;

    /**
     * Reprint required
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="reprint_required", nullable=false, options={"default": 0})
     */
    protected $reprintRequired = 0;

    /**
     * Set the is copy
     *
     * @param string $isCopy
     * @return PsvDisc
     */
    public function setIsCopy($isCopy)
    {
        $this->isCopy = $isCopy;

        return $this;
    }

    /**
     * Get the is copy
     *
     * @return string
     */
    public function getIsCopy()
    {
        return $this->isCopy;
    }

    /**
     * Set the licence
     *
     * @param \Olcs\Db\Entity\Licence $licence
     * @return PsvDisc
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
     * Set the reprint required
     *
     * @param string $reprintRequired
     * @return PsvDisc
     */
    public function setReprintRequired($reprintRequired)
    {
        $this->reprintRequired = $reprintRequired;

        return $this;
    }

    /**
     * Get the reprint required
     *
     * @return string
     */
    public function getReprintRequired()
    {
        return $this->reprintRequired;
    }
}
