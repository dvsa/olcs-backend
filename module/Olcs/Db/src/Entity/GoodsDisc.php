<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * GoodsDisc Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="goods_disc",
 *    indexes={
 *        @ORM\Index(name="fk_goods_disc_licence_vehicle1_idx", columns={"licence_vehicle_id"}),
 *        @ORM\Index(name="fk_goods_disc_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_goods_disc_user2_idx", columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_goods_disc_ref_data1_idx", columns={"removal_reason"}),
 *        @ORM\Index(name="fk_goods_disc_ref_data2_idx", columns={"removal_explanation"})
 *    }
 * )
 */
class GoodsDisc implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\RemovalExplanationManyToOne,
        Traits\RemovalReasonManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\LicenceVehicleManyToOne,
        Traits\DiscNo50Field,
        Traits\IssuedDateFieldAlt1,
        Traits\CeasedDateField,
        Traits\IsInterimField,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Is copy
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="is_copy", nullable=false)
     */
    protected $isCopy = 0;

    /**
     * Requested by self service user
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="requested_by_self_service_user", nullable=false)
     */
    protected $requestedBySelfServiceUser = 0;

    /**
     * Reprint required
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="reprint_required", nullable=false)
     */
    protected $reprintRequired = 0;

    /**
     * Set the is copy
     *
     * @param boolean $isCopy
     * @return \Olcs\Db\Entity\GoodsDisc
     */
    public function setIsCopy($isCopy)
    {
        $this->isCopy = $isCopy;

        return $this;
    }

    /**
     * Get the is copy
     *
     * @return boolean
     */
    public function getIsCopy()
    {
        return $this->isCopy;
    }

    /**
     * Set the requested by self service user
     *
     * @param boolean $requestedBySelfServiceUser
     * @return \Olcs\Db\Entity\GoodsDisc
     */
    public function setRequestedBySelfServiceUser($requestedBySelfServiceUser)
    {
        $this->requestedBySelfServiceUser = $requestedBySelfServiceUser;

        return $this;
    }

    /**
     * Get the requested by self service user
     *
     * @return boolean
     */
    public function getRequestedBySelfServiceUser()
    {
        return $this->requestedBySelfServiceUser;
    }

    /**
     * Set the reprint required
     *
     * @param boolean $reprintRequired
     * @return \Olcs\Db\Entity\GoodsDisc
     */
    public function setReprintRequired($reprintRequired)
    {
        $this->reprintRequired = $reprintRequired;

        return $this;
    }

    /**
     * Get the reprint required
     *
     * @return boolean
     */
    public function getReprintRequired()
    {
        return $this->reprintRequired;
    }
}
