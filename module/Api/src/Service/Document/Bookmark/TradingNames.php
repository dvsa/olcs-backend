<?php

namespace Dvsa\Olcs\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Service\Document\Bookmark\Base\DynamicBookmark;
use Dvsa\Olcs\Api\Domain\Query\Bookmark\LicenceBundle as Qry;

/**
 * Trading names bookmark
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class TradingNames extends DynamicBookmark
{
    public function getQuery(array $data)
    {
        $bundle = [
            'organisation' => [
                'tradingNames'
            ]
        ];

        return Qry::create(['id' => $data['licence'], 'bundle' => $bundle]);
    }

    public function render()
    {
        if (isset($this->data['organisation']['tradingNames'])) {
            $names = [];
            foreach ($this->data['organisation']['tradingNames'] as $tn) {
                $names[] = $tn['name'];
            }
            return implode(', ', $names);
        }
        return '';
    }
}
