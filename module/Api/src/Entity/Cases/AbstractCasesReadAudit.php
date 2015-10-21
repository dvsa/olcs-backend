<?php

namespace Dvsa\Olcs\Api\Entity\Cases;

use Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface;
use JsonSerializable;
use Dvsa\Olcs\Api\Entity\Traits\BundleSerializableTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * CasesReadAudit Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="cases_read_audit",
 *    indexes={
 *        @ORM\Index(name="ix_audit_read_cases_cases_id", columns={"cases_id"}),
 *        @ORM\Index(name="ix_audit_read_cases_user_id", columns={"user_id"}),
 *        @ORM\Index(name="ix_audit_read_cases_created_on", columns={"created_on"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_audit_read_cases_cases_id_user_id_created_on",
     *     columns={"cases_id","user_id","created_on"})
 *    }
 * )
 */
abstract class AbstractCasesReadAudit implements BundleSerializableInterface, JsonSerializable
{
    use BundleSerializableTrait;

    /**
     * Cases
     *
     * @var \Dvsa\Olcs\Api\Entity\Cases\Cases
     *
     * @ORM\ManyToOne(
     *     targetEntity="Dvsa\Olcs\Api\Entity\Cases\Cases",
     *     fetch="LAZY",
     *     inversedBy="readAudits"
     * )
     * @ORM\JoinColumn(name="cases_id", referencedColumnName="id", nullable=false)
     */
    protected $cases;

    /**
     * Created on
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="created_on", nullable=false)
     */
    protected $createdOn;

    /**
     * Identifier - Id
     *
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * User
     *
     * @var \Dvsa\Olcs\Api\Entity\User\User
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\User\User", fetch="LAZY")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    protected $user;

    /**
     * Set the cases
     *
     * @param \Dvsa\Olcs\Api\Entity\Cases\Cases $cases
     * @return CasesReadAudit
     */
    public function setCases($cases)
    {
        $this->cases = $cases;

        return $this;
    }

    /**
     * Get the cases
     *
     * @return \Dvsa\Olcs\Api\Entity\Cases\Cases
     */
    public function getCases()
    {
        return $this->cases;
    }

    /**
     * Set the created on
     *
     * @param \DateTime $createdOn
     * @return CasesReadAudit
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    /**
     * Get the created on
     *
     * @return \DateTime
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     * Set the id
     *
     * @param int $id
     * @return CasesReadAudit
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the user
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $user
     * @return CasesReadAudit
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get the user
     *
     * @return \Dvsa\Olcs\Api\Entity\User\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set the createdOn field on persist
     *
     * @ORM\PrePersist
     */
    public function setCreatedOnBeforePersist()
    {
        $this->createdOn = new \DateTime();
    }


    /**
     * Clear properties
     *
     * @param type $properties
     */
    public function clearProperties($properties = array())
    {
        foreach ($properties as $property) {

            if (property_exists($this, $property)) {
                if ($this->$property instanceof Collection) {

                    $this->$property = new ArrayCollection(array());

                } else {

                    $this->$property = null;
                }
            }
        }
    }
}
