<?php

namespace Dvsa\Olcs\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Service\Document\Bookmark\Base\DynamicBookmark;
use Dvsa\Olcs\Api\Entity\ContactDetails\PhoneContact;
use Dvsa\Olcs\Api\Domain\Query\Bookmark\LicenceBundle as Qry;

/**
 * Traffic Area (with phone number) Address bookmark
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class TaAddressPhone extends DynamicBookmark
{
    public function getQuery(array $data)
    {
        $bundle = [
            'trafficArea' => [
                'contactDetails' => [
                    'address',
                    'phoneContacts' => [
                        'phoneContactType'
                    ]
                ]
            ]
        ];
        return Qry::create(['id' => $data['licence'], 'bundle' => $bundle]);
    }

    public function render()
    {
        $trafficArea = $this->data['trafficArea'];
        $contactDetails = $trafficArea['contactDetails'];
        $address = isset($contactDetails['address']) ? $contactDetails['address'] : [];

        return implode(
            "\n",
            array_filter(
                [
                    $trafficArea['name'],
                    Formatter\Address::format($address),
                    $this->fetchTelephone()
                ]
            )
        );
    }

    private function fetchTelephone()
    {
        if (empty($this->data['trafficArea']['contactDetails']['phoneContacts'])) {
            return '';
        }

        foreach ($this->data['trafficArea']['contactDetails']['phoneContacts'] as $phone) {
            if ($phone['phoneContactType']['id'] === PhoneContact::TYPE_BUSINESS) {
                return $phone['phoneNumber'];
            }
        }
    }
}
