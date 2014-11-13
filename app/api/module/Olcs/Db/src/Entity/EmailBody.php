<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EmailBody Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="email_body",
 *    indexes={
 *        @ORM\Index(name="fk_email_body_email1_idx", 
 *            columns={"email_id"})
 *    }
 * )
 */
class EmailBody implements Interfaces\EntityInterface
{

    /**
     * Seq
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="seq", nullable=false)
     */
    protected $seq;

    /**
     * Text
     *
     * @var string
     *
     * @ORM\Column(type="string", name="text", length=8000, nullable=true)
     */
    protected $text;

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
     * Set the seq
     *
     * @param int $seq
     * @return EmailBody
     */
    public function setSeq($seq)
    {
        $this->seq = $seq;

        return $this;
    }

    /**
     * Get the seq
     *
     * @return int
     */
    public function getSeq()
    {
        return $this->seq;
    }

    /**
     * Set the text
     *
     * @param string $text
     * @return EmailBody
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get the text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
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
