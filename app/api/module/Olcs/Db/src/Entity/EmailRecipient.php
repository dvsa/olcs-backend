<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * EmailRecipient Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="email_recipient",
 *    indexes={
 *        @ORM\Index(name="IDX_670F6462A832C1C9", columns={"email_id"})
 *    }
 * )
 */
class EmailRecipient implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\EmailManyToOne;

    /**
     * Type
     *
     * @var int
     *
     * @ORM\Column(type="smallint", name="type", nullable=false)
     */
    protected $type;

    /**
     * Email address
     *
     * @var string
     *
     * @ORM\Column(type="string", name="email_address", length=255, nullable=false)
     */
    protected $emailAddress;

    /**
     * Set the type
     *
     * @param int $type
     * @return EmailRecipient
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get the type
     *
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the email address
     *
     * @param string $emailAddress
     * @return EmailRecipient
     */
    public function setEmailAddress($emailAddress)
    {
        $this->emailAddress = $emailAddress;

        return $this;
    }

    /**
     * Get the email address
     *
     * @return string
     */
    public function getEmailAddress()
    {
        return $this->emailAddress;
    }
}
