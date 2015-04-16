<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * CompaniesHouseRequest Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="companies_house_request")
 */
class CompaniesHouseRequest implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity;

    /**
     * Ip address
     *
     * @var string
     *
     * @ORM\Column(type="string", name="ip_address", length=255, nullable=true)
     */
    protected $ipAddress;

    /**
     * Request error
     *
     * @var string
     *
     * @ORM\Column(type="string", name="request_error", length=255, nullable=true)
     */
    protected $requestError;

    /**
     * Request type
     *
     * @var string
     *
     * @ORM\Column(type="string", name="request_type", length=255, nullable=true)
     */
    protected $requestType;

    /**
     * Requested on
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="requested_on", nullable=true)
     */
    protected $requestedOn;

    /**
     * Transaction id
     *
     * @var string
     *
     * @ORM\Column(type="string", name="transaction_id", length=32, nullable=true)
     */
    protected $transactionId;

    /**
     * Set the ip address
     *
     * @param string $ipAddress
     * @return CompaniesHouseRequest
     */
    public function setIpAddress($ipAddress)
    {
        $this->ipAddress = $ipAddress;

        return $this;
    }

    /**
     * Get the ip address
     *
     * @return string
     */
    public function getIpAddress()
    {
        return $this->ipAddress;
    }

    /**
     * Set the request error
     *
     * @param string $requestError
     * @return CompaniesHouseRequest
     */
    public function setRequestError($requestError)
    {
        $this->requestError = $requestError;

        return $this;
    }

    /**
     * Get the request error
     *
     * @return string
     */
    public function getRequestError()
    {
        return $this->requestError;
    }

    /**
     * Set the request type
     *
     * @param string $requestType
     * @return CompaniesHouseRequest
     */
    public function setRequestType($requestType)
    {
        $this->requestType = $requestType;

        return $this;
    }

    /**
     * Get the request type
     *
     * @return string
     */
    public function getRequestType()
    {
        return $this->requestType;
    }

    /**
     * Set the requested on
     *
     * @param \DateTime $requestedOn
     * @return CompaniesHouseRequest
     */
    public function setRequestedOn($requestedOn)
    {
        $this->requestedOn = $requestedOn;

        return $this;
    }

    /**
     * Get the requested on
     *
     * @return \DateTime
     */
    public function getRequestedOn()
    {
        return $this->requestedOn;
    }

    /**
     * Set the transaction id
     *
     * @param string $transactionId
     * @return CompaniesHouseRequest
     */
    public function setTransactionId($transactionId)
    {
        $this->transactionId = $transactionId;

        return $this;
    }

    /**
     * Get the transaction id
     *
     * @return string
     */
    public function getTransactionId()
    {
        return $this->transactionId;
    }
}
