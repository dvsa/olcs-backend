<?php

namespace Dvsa\Olcs\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Domain\Query\Bookmark\ApplicationBundle as Qry;

/**
 * Applicationtype bookmark
 */
class Applicationtype extends SingleValueAbstract
{
    const SRCH_VAL_KEY = 'application';
    const QUERY_CLASS = Qry::class;

    /**
     * Render bookmark
     *
     * @return string
     */
    public function render()
    {
        return $this->data['licenceType']['description'];
    }
}
