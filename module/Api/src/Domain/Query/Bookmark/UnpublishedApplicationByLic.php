<?php

namespace Dvsa\Olcs\Api\Domain\Query\Bookmark;

use Dvsa\Olcs\Transfer\Query\AbstractQuery;

/**
 * Unpublished Application by Licence Id
 */
class UnpublishedApplicationByLic extends AbstractQuery
{
    protected $licence;
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
    public function getLicence()
    {
        return $this->licence;
    }

    /**
     * @return mixed
     */
    public function getBundle()
    {
        return $this->bundle;
    }
}
