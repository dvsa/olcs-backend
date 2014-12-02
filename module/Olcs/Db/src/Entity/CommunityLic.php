<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * CommunityLic Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="community_lic",
 *    indexes={
 *        @ORM\Index(name="fk_community_lic_licence1_idx", columns={"licence_id"}),
 *        @ORM\Index(name="fk_community_lic_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_community_lic_user2_idx", columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_community_lic_ref_data1_idx", columns={"com_lic_status"})
 *    }
 * )
 */
class CommunityLic implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\LicenceManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Com lic status
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="com_lic_status", referencedColumnName="id", nullable=false)
     */
    protected $comLicStatus;

    /**
     * Expired date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="expired_date", nullable=true)
     */
    protected $expiredDate;

    /**
     * Specified date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="specified_date", nullable=true)
     */
    protected $specifiedDate;

    /**
     * Licence expired date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="licence_expired_date", nullable=true)
     */
    protected $licenceExpiredDate;

    /**
     * Issue no
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="issue_no", nullable=true)
     */
    protected $issueNo;

    /**
     * Serial no
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="serial_no", nullable=true)
     */
    protected $serialNo;

    /**
     * Serial no prefix
     *
     * @var string
     *
     * @ORM\Column(type="string", name="serial_no_prefix", length=4, nullable=true)
     */
    protected $serialNoPrefix;

    /**
     * Set the com lic status
     *
     * @param \Olcs\Db\Entity\RefData $comLicStatus
     * @return CommunityLic
     */
    public function setComLicStatus($comLicStatus)
    {
        $this->comLicStatus = $comLicStatus;

        return $this;
    }

    /**
     * Get the com lic status
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getComLicStatus()
    {
        return $this->comLicStatus;
    }

    /**
     * Set the expired date
     *
     * @param \DateTime $expiredDate
     * @return CommunityLic
     */
    public function setExpiredDate($expiredDate)
    {
        $this->expiredDate = $expiredDate;

        return $this;
    }

    /**
     * Get the expired date
     *
     * @return \DateTime
     */
    public function getExpiredDate()
    {
        return $this->expiredDate;
    }

    /**
     * Set the specified date
     *
     * @param \DateTime $specifiedDate
     * @return CommunityLic
     */
    public function setSpecifiedDate($specifiedDate)
    {
        $this->specifiedDate = $specifiedDate;

        return $this;
    }

    /**
     * Get the specified date
     *
     * @return \DateTime
     */
    public function getSpecifiedDate()
    {
        return $this->specifiedDate;
    }

    /**
     * Set the licence expired date
     *
     * @param \DateTime $licenceExpiredDate
     * @return CommunityLic
     */
    public function setLicenceExpiredDate($licenceExpiredDate)
    {
        $this->licenceExpiredDate = $licenceExpiredDate;

        return $this;
    }

    /**
     * Get the licence expired date
     *
     * @return \DateTime
     */
    public function getLicenceExpiredDate()
    {
        return $this->licenceExpiredDate;
    }

    /**
     * Set the issue no
     *
     * @param int $issueNo
     * @return CommunityLic
     */
    public function setIssueNo($issueNo)
    {
        $this->issueNo = $issueNo;

        return $this;
    }

    /**
     * Get the issue no
     *
     * @return int
     */
    public function getIssueNo()
    {
        return $this->issueNo;
    }

    /**
     * Set the serial no
     *
     * @param int $serialNo
     * @return CommunityLic
     */
    public function setSerialNo($serialNo)
    {
        $this->serialNo = $serialNo;

        return $this;
    }

    /**
     * Get the serial no
     *
     * @return int
     */
    public function getSerialNo()
    {
        return $this->serialNo;
    }

    /**
     * Set the serial no prefix
     *
     * @param string $serialNoPrefix
     * @return CommunityLic
     */
    public function setSerialNoPrefix($serialNoPrefix)
    {
        $this->serialNoPrefix = $serialNoPrefix;

        return $this;
    }

    /**
     * Get the serial no prefix
     *
     * @return string
     */
    public function getSerialNoPrefix()
    {
        return $this->serialNoPrefix;
    }
}
