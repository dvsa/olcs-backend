<?php

/**
 * Publish an Impounding
 */
namespace Dvsa\Olcs\Api\Domain\Command\Publication;

use Dvsa\Olcs\Api\Domain\Command\AbstractIdOnlyCommand;

/**
 * Publish an Impounding
 */
final class Impounding extends AbstractIdOnlyCommand
{
    protected $trafficAreas;
    protected $pubType;
    protected $licenceId;
    protected $applicationId;
    protected $publicInquiryId;

    /**
     * @return string
     */
    public function getTrafficArea()
    {
        return $this->trafficArea;
    }

    /**
     * @return array
     */
    public function getPubType()
    {
        return $this->pubType;
    }

    /**
     * @return mixed
     */
    public function getLicenceId()
    {
        return $this->licenceId;
    }

    /**
     * @return mixed
     */
    public function getApplicationId()
    {
        return $this->applicationId;
    }

    /**
     * @return mixed
     */
    public function getPublicInquiryId()
    {
        return $this->publicInquiryId;
    }
}
