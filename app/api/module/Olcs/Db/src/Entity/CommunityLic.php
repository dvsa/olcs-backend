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
 *        @ORM\Index(name="fk_community_lic_user2_idx", columns={"last_modified_by"})
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
        Traits\SpecifiedDateField,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Expired date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="expired_date", nullable=true)
     */
    protected $expiredDate;

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
     * Status
     *
     * @var string
     *
     * @ORM\Column(type="string", name="status", length=10, nullable=true)
     */
    protected $status;

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

    /**
     * Set the status
     *
     * @param string $status
     * @return CommunityLic
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get the status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }
}
