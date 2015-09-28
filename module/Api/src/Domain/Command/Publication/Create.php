<?php

/**
 * Create a publication
 */
namespace Dvsa\Olcs\Api\Domain\Command\Publication;

use Dvsa\Olcs\Transfer\Command\AbstractCommand;

/**
 * Create a publication
 */
final class Create extends AbstractCommand
{
    protected $trafficArea;
    protected $pubStatus;
    protected $pubDate;
    protected $pubType;
    protected $publicationNo;
    protected $docTemplate;

    /**
     * @return string
     */
    public function getTrafficArea()
    {
        return $this->trafficArea;
    }

    /**
     * @return string
     */
    public function getPubStatus()
    {
        return $this->pubStatus;
    }

    /**
     * @return string
     */
    public function getPubDate()
    {
        return $this->pubDate;
    }

    /**
     * @return string
     */
    public function getPubType()
    {
        return $this->pubType;
    }

    /**
     * @return int
     */
    public function getPublicationNo()
    {
        return $this->publicationNo;
    }

    /**
     * @return int
     */
    public function getDocTemplate()
    {
        return $this->docTemplate;
    }
}
