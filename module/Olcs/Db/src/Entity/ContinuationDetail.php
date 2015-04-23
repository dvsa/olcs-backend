<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * ContinuationDetail Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="continuation_detail",
 *    indexes={
 *        @ORM\Index(name="ix_continuation_detail_continuation_id", columns={"continuation_id"}),
 *        @ORM\Index(name="ix_continuation_detail_licence_id", columns={"licence_id"}),
 *        @ORM\Index(name="ix_continuation_detail_status", columns={"status"}),
 *        @ORM\Index(name="ix_continuation_detail_acceptable", columns={"acceptable"}),
 *        @ORM\Index(name="ix_continuation_detail_received", columns={"received"}),
 *        @ORM\Index(name="ix_continuation_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_continuation_last_modified_by", columns={"last_modified_by"})
 *    }
 * )
 */
class ContinuationDetail implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\LicenceManyToOne,
        Traits\StatusManyToOneAlt1,
        Traits\CustomVersionField;

    /**
     * Acceptable
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="acceptable", nullable=false, options={"default": 0})
     */
    protected $acceptable = 0;

    /**
     * Continuation
     *
     * @var \Olcs\Db\Entity\Continuation
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Continuation")
     * @ORM\JoinColumn(name="continuation_id", referencedColumnName="id", nullable=false)
     */
    protected $continuation;

    /**
     * Received
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="received", nullable=false, options={"default": 0})
     */
    protected $received = 0;

    /**
     * Set the acceptable
     *
     * @param string $acceptable
     * @return ContinuationDetail
     */
    public function setAcceptable($acceptable)
    {
        $this->acceptable = $acceptable;

        return $this;
    }

    /**
     * Get the acceptable
     *
     * @return string
     */
    public function getAcceptable()
    {
        return $this->acceptable;
    }

    /**
     * Set the continuation
     *
     * @param \Olcs\Db\Entity\Continuation $continuation
     * @return ContinuationDetail
     */
    public function setContinuation($continuation)
    {
        $this->continuation = $continuation;

        return $this;
    }

    /**
     * Get the continuation
     *
     * @return \Olcs\Db\Entity\Continuation
     */
    public function getContinuation()
    {
        return $this->continuation;
    }

    /**
     * Set the received
     *
     * @param string $received
     * @return ContinuationDetail
     */
    public function setReceived($received)
    {
        $this->received = $received;

        return $this;
    }

    /**
     * Get the received
     *
     * @return string
     */
    public function getReceived()
    {
        return $this->received;
    }
}
