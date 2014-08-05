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
 *        @ORM\Index(name="fk_email_recipient_email1_idx", columns={"email_id"})
 *    }
 * )
 */
class EmailRecipient implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity;

    /**
     * Email
     *
     * @var \Olcs\Db\Entity\Email
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Email")
     * @ORM\JoinColumn(name="email_id", referencedColumnName="id")
     */
    protected $email;

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
     * Set the email
     *
     * @param \Olcs\Db\Entity\Email $email
     * @return \Olcs\Db\Entity\EmailRecipient
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the email
     *
     * @return \Olcs\Db\Entity\Email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set the type
     *
     * @param int $type
     * @return \Olcs\Db\Entity\EmailRecipient
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
     * @return \Olcs\Db\Entity\EmailRecipient
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
