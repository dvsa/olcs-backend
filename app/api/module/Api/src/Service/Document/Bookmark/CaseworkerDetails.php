<?php

namespace Dvsa\Olcs\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Service\Document\Bookmark\Base\DynamicBookmark;
use Dvsa\Olcs\Api\Domain\Query\Bookmark\UserBundle as Qry;

/**
 * Caseworker details bookmark
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class CaseworkerDetails extends DynamicBookmark
{
    // makes our ref data key a bit clearer in context
    const TEL_DIRECT_DIAL ='phone_t_tel';

    /**
     * Get Query
     *
     * @param array $data Query Parameters
     *
     * @return static
     */
    public function getQuery(array $data)
    {
        $bundle = [
            'contactDetails' => [
                /**
                 * 1) Preferred address; directly linked against a user
                 */
                'address' => [],
                'phoneContacts' => [
                    'phoneContactType'
                ],
                'person'
            ],
            'team' => [
                'trafficArea' => [
                    'contactDetails' => [
                        /**
                         * 2) Fallback address; linked traffic area
                         */
                        'address'
                    ]
                ]
            ]
        ];
        return Qry::create(['id' => $data['user'], 'bundle' => $bundle]);
    }

    /**
     * Render
     *
     * @return string
     */
    public function render()
    {
        $directDial = $this->fetchDirectDial();

        $address = $this->fetchBestAddress();

        $taName = isset($this->data['team']['trafficArea']['name'])
            ? $this->data['team']['trafficArea']['name']
            : '';

        $details = $this->data['contactDetails'];

        return implode(
            "\n",
            array_filter(
                [
                    Formatter\Name::format($details['person']),
                    $taName,
                    Formatter\Address::format($address),
                    'Direct Line: ' . $directDial,
                    'e-mail: ' . $details['emailAddress']
                ]
            )
        );
    }

    /**
     * Fetch Best Address
     *
     * @return string
     */
    private function fetchBestAddress()
    {
        // we prefer an address directly linked against the user...
        if (!empty($this->data['contactDetails']['address'])) {
            return $this->data['contactDetails']['address'];
        }

        // but if not, fall back to the one against the team's TA
        return $this->data['team']['trafficArea']['contactDetails']['address'];
    }

    /**
     * Fetch Direct Dial
     *
     * @return null|string
     */
    private function fetchDirectDial()
    {
        if (empty($this->data['contactDetails']['phoneContacts'])) {
            return '';
        }
        foreach ($this->data['contactDetails']['phoneContacts'] as $phone) {
            if ($phone['phoneContactType']['id'] === self::TEL_DIRECT_DIAL) {
                return $phone['phoneNumber'];
            }
        }
        return null;
    }
}
