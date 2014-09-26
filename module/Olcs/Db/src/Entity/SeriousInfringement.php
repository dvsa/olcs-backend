<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * SeriousInfringement Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedDate", timeAware=true)
 * @ORM\Table(name="serious_infringement",
 *    indexes={
 *        @ORM\Index(name="IDX_106EA68CE71044B9", columns={"erru_response_user_id"}),
 *        @ORM\Index(name="IDX_106EA68C7CE276A9", columns={"si_category_type_id"}),
 *        @ORM\Index(name="IDX_106EA68C8B8A7B7", columns={"member_state_code"}),
 *        @ORM\Index(name="IDX_106EA68CF9FDD69C", columns={"si_category_id"}),
 *        @ORM\Index(name="IDX_106EA68CDE12AB56", columns={"created_by"}),
 *        @ORM\Index(name="IDX_106EA68CCF10D4F5", columns={"case_id"}),
 *        @ORM\Index(name="IDX_106EA68C65CF370E", columns={"last_modified_by"})
 *    }
 * )
 */
class SeriousInfringement implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\SiCategoryManyToOne,
        Traits\CreatedByManyToOne,
        Traits\CaseManyToOneAlt1,
        Traits\LastModifiedByManyToOne,
        Traits\CustomDeletedDateField,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Erru response user
     *
     * @var \Olcs\Db\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\User", fetch="LAZY")
     * @ORM\JoinColumn(name="erru_response_user_id", referencedColumnName="id", nullable=true)
     */
    protected $erruResponseUser;

    /**
     * Si category type
     *
     * @var \Olcs\Db\Entity\SiCategoryType
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\SiCategoryType", fetch="LAZY")
     * @ORM\JoinColumn(name="si_category_type_id", referencedColumnName="id", nullable=false)
     */
    protected $siCategoryType;

    /**
     * Member state code
     *
     * @var \Olcs\Db\Entity\Country
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Country", fetch="LAZY")
     * @ORM\JoinColumn(name="member_state_code", referencedColumnName="id", nullable=true)
     */
    protected $memberStateCode;

    /**
     * Check date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="check_date", nullable=true)
     */
    protected $checkDate;

    /**
     * Erru response sent
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="erru_response_sent", nullable=false)
     */
    protected $erruResponseSent;

    /**
     * Erru response time
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="erru_response_time", nullable=true)
     */
    protected $erruResponseTime;

    /**
     * Infringement date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="infringement_date", nullable=true)
     */
    protected $infringementDate;

    /**
     * Notification number
     *
     * @var string
     *
     * @ORM\Column(type="string", name="notification_number", length=36, nullable=true)
     */
    protected $notificationNumber;

    /**
     * Reason
     *
     * @var string
     *
     * @ORM\Column(type="string", name="reason", length=500, nullable=true)
     */
    protected $reason;

    /**
     * Set the erru response user
     *
     * @param \Olcs\Db\Entity\User $erruResponseUser
     * @return SeriousInfringement
     */
    public function setErruResponseUser($erruResponseUser)
    {
        $this->erruResponseUser = $erruResponseUser;

        return $this;
    }

    /**
     * Get the erru response user
     *
     * @return \Olcs\Db\Entity\User
     */
    public function getErruResponseUser()
    {
        return $this->erruResponseUser;
    }

    /**
     * Set the si category type
     *
     * @param \Olcs\Db\Entity\SiCategoryType $siCategoryType
     * @return SeriousInfringement
     */
    public function setSiCategoryType($siCategoryType)
    {
        $this->siCategoryType = $siCategoryType;

        return $this;
    }

    /**
     * Get the si category type
     *
     * @return \Olcs\Db\Entity\SiCategoryType
     */
    public function getSiCategoryType()
    {
        return $this->siCategoryType;
    }

    /**
     * Set the member state code
     *
     * @param \Olcs\Db\Entity\Country $memberStateCode
     * @return SeriousInfringement
     */
    public function setMemberStateCode($memberStateCode)
    {
        $this->memberStateCode = $memberStateCode;

        return $this;
    }

    /**
     * Get the member state code
     *
     * @return \Olcs\Db\Entity\Country
     */
    public function getMemberStateCode()
    {
        return $this->memberStateCode;
    }

    /**
     * Set the check date
     *
     * @param \DateTime $checkDate
     * @return SeriousInfringement
     */
    public function setCheckDate($checkDate)
    {
        $this->checkDate = $checkDate;

        return $this;
    }

    /**
     * Get the check date
     *
     * @return \DateTime
     */
    public function getCheckDate()
    {
        return $this->checkDate;
    }

    /**
     * Set the erru response sent
     *
     * @param string $erruResponseSent
     * @return SeriousInfringement
     */
    public function setErruResponseSent($erruResponseSent)
    {
        $this->erruResponseSent = $erruResponseSent;

        return $this;
    }

    /**
     * Get the erru response sent
     *
     * @return string
     */
    public function getErruResponseSent()
    {
        return $this->erruResponseSent;
    }

    /**
     * Set the erru response time
     *
     * @param \DateTime $erruResponseTime
     * @return SeriousInfringement
     */
    public function setErruResponseTime($erruResponseTime)
    {
        $this->erruResponseTime = $erruResponseTime;

        return $this;
    }

    /**
     * Get the erru response time
     *
     * @return \DateTime
     */
    public function getErruResponseTime()
    {
        return $this->erruResponseTime;
    }

    /**
     * Set the infringement date
     *
     * @param \DateTime $infringementDate
     * @return SeriousInfringement
     */
    public function setInfringementDate($infringementDate)
    {
        $this->infringementDate = $infringementDate;

        return $this;
    }

    /**
     * Get the infringement date
     *
     * @return \DateTime
     */
    public function getInfringementDate()
    {
        return $this->infringementDate;
    }

    /**
     * Set the notification number
     *
     * @param string $notificationNumber
     * @return SeriousInfringement
     */
    public function setNotificationNumber($notificationNumber)
    {
        $this->notificationNumber = $notificationNumber;

        return $this;
    }

    /**
     * Get the notification number
     *
     * @return string
     */
    public function getNotificationNumber()
    {
        return $this->notificationNumber;
    }

    /**
     * Set the reason
     *
     * @param string $reason
     * @return SeriousInfringement
     */
    public function setReason($reason)
    {
        $this->reason = $reason;

        return $this;
    }

    /**
     * Get the reason
     *
     * @return string
     */
    public function getReason()
    {
        return $this->reason;
    }
}
