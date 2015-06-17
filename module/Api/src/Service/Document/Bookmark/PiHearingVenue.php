<?php

namespace Dvsa\Olcs\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Service\Document\Bookmark\Base\DynamicBookmark;
use Dvsa\Olcs\Api\Domain\Query\Bookmark\PiHearingBundle as Qry;

/**
 * PiHearingVenue
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
class PiHearingVenue extends DynamicBookmark
{
    public function getQuery(array $data)
    {
        if (!isset($data['hearing'])) {
            return null;
        }

        return Qry::create(['id' => $data['hearing'], 'bundle' => ['piVenue']]);
    }

    public function render()
    {
        if (isset($this->data['piVenue']) && count($this->data['piVenue']) > 0) {
            return $this->data['piVenue']['name'];
        }

        return $this->data['venueOther'];
    }
}
