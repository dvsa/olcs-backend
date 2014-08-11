<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * CardPaymentTokenUsage Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="card_payment_token_usage",
 *    indexes={
 *        @ORM\Index(name="IDX_FD3FFE7A64F8C732", columns={"payment_uid"}),
 *        @ORM\Index(name="IDX_FD3FFE7ADE12AB56", columns={"created_by"}),
 *        @ORM\Index(name="IDX_FD3FFE7A65CF370E", columns={"last_modified_by"})
 *    }
 * )
 */
class CardPaymentTokenUsage implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\CreatedByManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Payment uid
     *
     * @var \Olcs\Db\Entity\Payment
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Payment")
     * @ORM\JoinColumn(name="payment_uid", referencedColumnName="id")
     */
    protected $paymentUid;

    /**
     * Token
     *
     * @var string
     *
     * @ORM\Column(type="string", name="token", length=255, nullable=false)
     */
    protected $token;

    /**
     * Status
     *
     * @var string
     *
     * @ORM\Column(type="string", name="status", length=255, nullable=false)
     */
    protected $status;


    /**
     * Set the payment uid
     *
     * @param \Olcs\Db\Entity\Payment $paymentUid
     * @return CardPaymentTokenUsage
     */
    public function setPaymentUid($paymentUid)
    {
        $this->paymentUid = $paymentUid;

        return $this;
    }

    /**
     * Get the payment uid
     *
     * @return \Olcs\Db\Entity\Payment
     */
    public function getPaymentUid()
    {
        return $this->paymentUid;
    }


    /**
     * Set the token
     *
     * @param string $token
     * @return CardPaymentTokenUsage
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get the token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }


    /**
     * Set the status
     *
     * @param string $status
     * @return CardPaymentTokenUsage
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get the status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

}
