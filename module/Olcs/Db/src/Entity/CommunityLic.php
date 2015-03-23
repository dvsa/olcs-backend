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
 *        @ORM\Index(name="ix_community_lic_licence_id", columns={"licence_id"}),
 *        @ORM\Index(name="ix_community_lic_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_community_lic_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_community_lic_com_lic_status", columns={"status"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_community_lic_olbs_key", columns={"olbs_key"})
 *    }
 * )
 */
class CommunityLic implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\OlbsKeyField,
        Traits\SpecifiedDateField,
        Traits\StatusManyToOne,
        Traits\CustomVersionField;

    /**
     * Expired date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="expired_date", nullable=true)
     */
    protected $expiredDate;

    /**
     * Issue no
     *
     * @var int
     *
     * @ORM\Column(type="smallint", name="issue_no", nullable=true)
     */
    protected $issueNo;

    /**
     * Licence
     *
     * @var \Olcs\Db\Entity\Licence
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Licence", inversedBy="communityLics")
     * @ORM\JoinColumn(name="licence_id", referencedColumnName="id", nullable=false)
     */
    protected $licence;

    /**
     * Licence expired date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="licence_expired_date", nullable=true)
     */
    protected $licenceExpiredDate;

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
     * Set the licence
     *
     * @param \Olcs\Db\Entity\Licence $licence
     * @return CommunityLic
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
