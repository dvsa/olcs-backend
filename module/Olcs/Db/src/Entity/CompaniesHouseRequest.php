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
     * Requested on
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="requested_on", nullable=true)
     */
    protected $requestedOn;

    /**
     * Request type
     *
     * @var string
     *
     * @ORM\Column(type="string", name="request_type", length=255, nullable=true)
     */
    protected $requestType;

    /**
     * Request error
     *
     * @var string
     *
     * @ORM\Column(type="string", name="request_error", length=255, nullable=true)
     */
    protected $requestError;

    /**
     * Ip address
     *
     * @var string
     *
     * @ORM\Column(type="string", name="ip_address", length=255, nullable=true)
     */
    protected $ipAddress;

    /**
     * Set the requested on
     *
     * @param \DateTime $requestedOn
     * @return \Olcs\Db\Entity\CompaniesHouseRequest
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
     * Set the request type
     *
     * @param string $requestType
     * @return \Olcs\Db\Entity\CompaniesHouseRequest
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
     * Set the request error
     *
     * @param string $requestError
     * @return \Olcs\Db\Entity\CompaniesHouseRequest
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
     * Set the ip address
     *
     * @param string $ipAddress
     * @return \Olcs\Db\Entity\CompaniesHouseRequest
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
}
