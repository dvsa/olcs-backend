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
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\EmailManyToOne;

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
}
