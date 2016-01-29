<?php

namespace Dvsa\Olcs\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Service\Document\Bookmark\Base\DynamicBookmark;
use Dvsa\Olcs\Api\Domain\Query\Bookmark\LicenceBundle as Qry;

/**
 * OpNameOnly bookmark
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class OpNameOnly extends DynamicBookmark
{
    public function getQuery(array $data)
    {
        return Qry::create(['id' => $data['licence'], 'bundle' => ['organisation']]);
    }

    public function render()
    {
        if (isset($this->data['organisation']['name'])) {
            return $this->data['organisation']['name'];
        }
        return '';
    }
}
