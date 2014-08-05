<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Email address60 field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait EmailAddress60Field
{
    /**
     * Email address
     *
     * @var string
     *
     * @ORM\Column(type="string", name="email_address", length=60, nullable=true)
     */
    protected $emailAddress;

    /**
     * Set the email address
     *
     * @param string $emailAddress
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
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
