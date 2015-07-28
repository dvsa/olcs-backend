<?php

namespace Dvsa\Olcs\Api\Domain\Query\Bookmark;

use Dvsa\Olcs\Transfer\Query\AbstractQuery;

/**
 * Unpublished Bus Reg
 */
class UnpublishedBusReg extends AbstractQuery
{
    protected $busReg;
    protected $publicationSection;
    protected $publication;

    protected $bundle = [];

    /**
     * @return int
     */
    public function getPublicationSection()
    {
        return $this->publicationSection;
    }

    /**
     * @return int
     */
    public function getPublication()
    {
        return $this->publication;
    }

    /**
     * @return int
     */
    public function getBusReg()
    {
        return $this->busReg;
    }

    /**
     * @return mixed
     */
    public function getBundle()
    {
        return $this->bundle;
    }
}
