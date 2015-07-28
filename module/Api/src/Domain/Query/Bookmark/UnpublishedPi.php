<?php

namespace Dvsa\Olcs\Api\Domain\Query\Bookmark;

use Dvsa\Olcs\Transfer\Query\AbstractQuery;

/**
 * Unpublished Pi
 */
class UnpublishedPi extends AbstractQuery
{
    protected $pi;
    protected $publicationSection;
    protected $publication;

    protected $bundle = [];

    /**
     * @return mixed
     */
    public function getPublicationSection()
    {
        return $this->publicationSection;
    }

    /**
     * @return mixed
     */
    public function getPublication()
    {
        return $this->publication;
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
    public function getBundle()
    {
        return $this->bundle;
    }
}
