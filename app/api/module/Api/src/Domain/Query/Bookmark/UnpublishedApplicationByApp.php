<?php

namespace Dvsa\Olcs\Api\Domain\Query\Bookmark;

use Dvsa\Olcs\Transfer\Query\AbstractQuery;

/**
 * Unpublished Application
 */
class UnpublishedApplicationByApp extends AbstractQuery
{
    protected $application;
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
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * @return mixed
     */
    public function getBundle()
    {
        return $this->bundle;
    }
}
