<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * FeeManualAlteration Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="fee_manual_alteration",
 *    indexes={
 *        @ORM\Index(name="fk_fee_manual_alteration_fee1_idx", columns={"fee_id"}),
 *        @ORM\Index(name="fk_fee_manual_alteration_ref_data1_idx", columns={"alteration_type"}),
 *        @ORM\Index(name="fk_fee_manual_alteration_ref_data2_idx", columns={"post_fee_status"}),
 *        @ORM\Index(name="fk_fee_manual_alteration_ref_data3_idx", columns={"pre_fee_status"}),
 *        @ORM\Index(name="fk_fee_manual_alteration_user1_idx", columns={"user_id"})
 *    }
 * )
 */
class FeeManualAlteration implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\UserManyToOne,
        Traits\FeeManyToOne;

    /**
     * Pre fee status
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="pre_fee_status", referencedColumnName="id", nullable=false)
     */
    protected $preFeeStatus;

    /**
     * Post fee status
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="post_fee_status", referencedColumnName="id", nullable=false)
     */
    protected $postFeeStatus;

    /**
     * Alteration type
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="alteration_type", referencedColumnName="id", nullable=false)
     */
    protected $alterationType;

    /**
     * Actioned date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="actioned_date", nullable=true)
     */
    protected $actionedDate;

    /**
     * Post received date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="post_received_date", nullable=true)
     */
    protected $postReceivedDate;

    /**
     * Post receipt no
     *
     * @var string
     *
     * @ORM\Column(type="string", name="post_receipt_no", length=45, nullable=true)
     */
    protected $postReceiptNo;

    /**
     * Post value
     *
     * @var float
     *
     * @ORM\Column(type="decimal", name="post_value", precision=10, scale=2, nullable=true)
     */
    protected $postValue;

    /**
     * Pre received date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="pre_received_date", nullable=true)
     */
    protected $preReceivedDate;

    /**
     * Pre receipt no
     *
     * @var string
     *
     * @ORM\Column(type="string", name="pre_receipt_no", length=45, nullable=true)
     */
    protected $preReceiptNo;

    /**
     * Pre value
     *
     * @var float
     *
     * @ORM\Column(type="decimal", name="pre_value", precision=10, scale=2, nullable=true)
     */
    protected $preValue;

    /**
     * Set the pre fee status
     *
     * @param \Olcs\Db\Entity\RefData $preFeeStatus
     * @return FeeManualAlteration
     */
    public function setPreFeeStatus($preFeeStatus)
    {
        $this->preFeeStatus = $preFeeStatus;

        return $this;
    }

    /**
     * Get the pre fee status
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getPreFeeStatus()
    {
        return $this->preFeeStatus;
    }

    /**
     * Set the post fee status
     *
     * @param \Olcs\Db\Entity\RefData $postFeeStatus
     * @return FeeManualAlteration
     */
    public function setPostFeeStatus($postFeeStatus)
    {
        $this->postFeeStatus = $postFeeStatus;

        return $this;
    }

    /**
     * Get the post fee status
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getPostFeeStatus()
    {
        return $this->postFeeStatus;
    }

    /**
     * Set the alteration type
     *
     * @param \Olcs\Db\Entity\RefData $alterationType
     * @return FeeManualAlteration
     */
    public function setAlterationType($alterationType)
    {
        $this->alterationType = $alterationType;

        return $this;
    }

    /**
     * Get the alteration type
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getAlterationType()
    {
        return $this->alterationType;
    }

    /**
     * Set the actioned date
     *
     * @param \DateTime $actionedDate
     * @return FeeManualAlteration
     */
    public function setActionedDate($actionedDate)
    {
        $this->actionedDate = $actionedDate;

        return $this;
    }

    /**
     * Get the actioned date
     *
     * @return \DateTime
     */
    public function getActionedDate()
    {
        return $this->actionedDate;
    }

    /**
     * Set the post received date
     *
     * @param \DateTime $postReceivedDate
     * @return FeeManualAlteration
     */
    public function setPostReceivedDate($postReceivedDate)
    {
        $this->postReceivedDate = $postReceivedDate;

        return $this;
    }

    /**
     * Get the post received date
     *
     * @return \DateTime
     */
    public function getPostReceivedDate()
    {
        return $this->postReceivedDate;
    }

    /**
     * Set the post receipt no
     *
     * @param string $postReceiptNo
     * @return FeeManualAlteration
     */
    public function setPostReceiptNo($postReceiptNo)
    {
        $this->postReceiptNo = $postReceiptNo;

        return $this;
    }

    /**
     * Get the post receipt no
     *
     * @return string
     */
    public function getPostReceiptNo()
    {
        return $this->postReceiptNo;
    }

    /**
     * Set the post value
     *
     * @param float $postValue
     * @return FeeManualAlteration
     */
    public function setPostValue($postValue)
    {
        $this->postValue = $postValue;

        return $this;
    }

    /**
     * Get the post value
     *
     * @return float
     */
    public function getPostValue()
    {
        return $this->postValue;
    }

    /**
     * Set the pre received date
     *
     * @param \DateTime $preReceivedDate
     * @return FeeManualAlteration
     */
    public function setPreReceivedDate($preReceivedDate)
    {
        $this->preReceivedDate = $preReceivedDate;

        return $this;
    }

    /**
     * Get the pre received date
     *
     * @return \DateTime
     */
    public function getPreReceivedDate()
    {
        return $this->preReceivedDate;
    }

    /**
     * Set the pre receipt no
     *
     * @param string $preReceiptNo
     * @return FeeManualAlteration
     */
    public function setPreReceiptNo($preReceiptNo)
    {
        $this->preReceiptNo = $preReceiptNo;

        return $this;
    }

    /**
     * Get the pre receipt no
     *
     * @return string
     */
    public function getPreReceiptNo()
    {
        return $this->preReceiptNo;
    }

    /**
     * Set the pre value
     *
     * @param float $preValue
     * @return FeeManualAlteration
     */
    public function setPreValue($preValue)
    {
        $this->preValue = $preValue;

        return $this;
    }

    /**
     * Get the pre value
     *
     * @return float
     */
    public function getPreValue()
    {
        return $this->preValue;
    }
}
