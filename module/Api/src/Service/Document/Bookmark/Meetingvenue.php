<?php

namespace Dvsa\Olcs\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Domain\Query\Bookmark\HearingBundle as Qry;

/**
 * Meetingvenue
 */
class Meetingvenue extends SingleValueAbstract
{
    public const SRCH_FLD_KEY = 'case';
    public const SRCH_VAL_KEY = 'case';
    public const QUERY_CLASS = Qry::class;
    public const BUNDLE = ['venue'];

    /**
     * Render the bookmark
     *
     * @return null|string
     */
    public function render()
    {
        if (isset($this->data['venue']['name'])) {
            return $this->data['venue']['name'];
        }

        return $this->data['venueOther'];
    }
}
