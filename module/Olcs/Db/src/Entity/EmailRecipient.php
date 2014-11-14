<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EmailRecipient Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="email_recipient",
 *    indexes={
 *        @ORM\Index(name="fk_email_recipient_email1_idx", 
 *            columns={"email_id"})
 *    }
 * )
 */
class EmailRecipient implements Interfaces\EntityInterface
{

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
     * Email
     *
     * @var \Olcs\Db\Entity\Email
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Email", fetch="LAZY")
     * @ORM\JoinColumn(name="email_id", referencedColumnName="id", nullable=false)
     */
    protected $email;

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

    /**
     * Set the id
     *
     * @param int $id
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
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
