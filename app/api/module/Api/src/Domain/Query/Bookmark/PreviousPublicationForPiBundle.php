<?php

namespace Dvsa\Olcs\Api\Domain\Query\Bookmark;

use Dvsa\Olcs\Transfer\Query\AbstractQuery;

/**
 * PreviousPublicationForPi Bundle
 */
class PreviousPublicationForPiBundle extends AbstractQuery
{
    protected $pi;
    protected $trafficArea;
    protected $publicationNo;
    protected $pubType;
    protected $bundle = [];

    /**
     * @return mixed
     */
    public function getTrafficArea()
    {
        return $this->trafficArea;
    }

    /**
     * @return mixed
     */
    public function getPi()
    {
        return $this->pi;
    }

    /**
     * @return mixed
     */
    public function getPubType()
    {
        return $this->pubType;
    }

    /**
     * @return mixed
     */
    public function getPublicationNo()
    {
        return $this->publicationNo;
    }

    /**
     * @return array
     */
    public function getBundle()
    {
        return $this->bundle;
    }
}
