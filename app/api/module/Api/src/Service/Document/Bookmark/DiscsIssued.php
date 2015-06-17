<?php

namespace Dvsa\Olcs\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Service\Document\Bookmark\Base\DynamicBookmark;
use Dvsa\Olcs\Api\Domain\Query\Bookmark\LicenceBundle as Qry;

/**
 * Licence - count of discs
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class DiscsIssued extends DynamicBookmark
{
    public function getQuery(array $data)
    {
        return Qry::create(['id' => $data['licence'], 'bundle' => ['psvDiscs']]);
    }

    public function render()
    {
        $activeDiscs = array_filter(
            $this->data['psvDiscs'],
            function ($val) {
                return $val['ceasedDate'] === null;
            }
        );
        return count($activeDiscs);
    }
}
