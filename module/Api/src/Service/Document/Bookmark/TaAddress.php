<?php

namespace Dvsa\Olcs\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Service\Document\Bookmark\Base\DynamicBookmark;
use Dvsa\Olcs\Api\Domain\Query\Bookmark\LicenceBundle as Qry;

/**
 * Traffic Area Address bookmark
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class TaAddress extends DynamicBookmark
{
    public function getQuery(array $data)
    {
        $bundle = [
            'trafficArea' => [
                'contactDetails' => [
                    'address'
                ]
            ]
        ];
        return Qry::create(['id' => $data['licence'], 'bundle' => $bundle]);
    }

    public function render()
    {
        $trafficArea = $this->data['trafficArea'];
        $address = isset($trafficArea['contactDetails']['address']) ? $trafficArea['contactDetails']['address'] : [];

        return implode(
            "\n",
            array_filter(
                [
                    $trafficArea['name'],
                    Formatter\Address::format($address)
                ]
            )
        );
    }
}
