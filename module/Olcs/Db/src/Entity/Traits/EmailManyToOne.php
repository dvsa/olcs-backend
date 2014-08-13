<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Email many to one trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait EmailManyToOne
{
    /**
     * Email
     *
     * @var \Olcs\Db\Entity\Email
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Email", fetch="LAZY")
     * @ORM\JoinColumn(name="email_id", referencedColumnName="id")
     */
    protected $email;

    /**
     * Set the email
     *
     * @param \Olcs\Db\Entity\Email $email
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
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

}
