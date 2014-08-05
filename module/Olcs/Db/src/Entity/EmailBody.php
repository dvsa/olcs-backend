<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * EmailBody Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="email_body",
 *    indexes={
 *        @ORM\Index(name="fk_email_body_email1_idx", columns={"email_id"})
 *    }
 * )
 */
class EmailBody implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity;

    /**
     * Identifier - Seq
     *
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer", name="seq")
     */
    protected $seq;

    /**
     * Identifier - Email
     *
     * @var \Olcs\Db\Entity\Email
     *
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="Olcs\Db\Entity\Email")
     * @ORM\JoinColumn(name="email_id", referencedColumnName="id")
     */
    protected $email;

    /**
     * Text
     *
     * @var string
     *
     * @ORM\Column(type="string", name="text", length=8000, nullable=true)
     */
    protected $text;

    /**
     * Set the seq
     *
     * @param int $seq
     * @return \Olcs\Db\Entity\EmailBody
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
     * Set the email
     *
     * @param \Olcs\Db\Entity\Email $email
     * @return \Olcs\Db\Entity\EmailBody
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
     * Set the text
     *
     * @param string $text
     * @return \Olcs\Db\Entity\EmailBody
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
}
