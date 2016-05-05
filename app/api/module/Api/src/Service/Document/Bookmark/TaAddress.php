<?php

namespace Dvsa\Olcs\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Service\Document\Bookmark\Base\DynamicBookmark;
use Dvsa\Olcs\Api\Domain\Query\Bookmark\LicenceBundle as LicenceQry;
use Dvsa\Olcs\Api\Domain\Query\Bookmark\UserBundle as UserQry;

/**
 * Traffic Area Address bookmark
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class TaAddress extends DynamicBookmark
{
    public function getQuery(array $data)
    {
        $userBundle = [
            'contactDetails' => [
                'address'
            ],
            'team' => [
                'trafficArea'
            ]
        ];
        $licenceBundle = [
            'trafficArea' => [
                'contactDetails' => [
                    'address'
                ]
            ]
        ];
        $userQry = UserQry::create(['id' => $data['user'], 'bundle' => $userBundle]);
        $licenceQry = LicenceQry::create(['id' => $data['licence'], 'bundle' => $licenceBundle]);

        return [$userQry, $licenceQry];
    }

    public function render()
    {
        $user = $this->data[0];
        $licence = $this->data[1];

        if (isset($user['contactDetails']['address'])) {
            $trafficArea = isset($licence['trafficArea']['name']) ? $licence['trafficArea']['name'] : '';
            $address = $user['contactDetails']['address'];
        } elseif (isset($licence['trafficArea']['contactDetails']['address'])) {
            $trafficArea = $licence['trafficArea']['name'];
            $address = $licence['trafficArea']['contactDetails']['address'];
        } else {
            $trafficArea = isset($licence['trafficArea']['name']) ? $licence['trafficArea']['name'] : '';
            $address = [];
        }

        return implode(
            "\n",
            array_filter(
                [
                    $trafficArea,
                    Formatter\Address::format($address)
                ]
            )
        );
    }
}
