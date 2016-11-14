<?php

namespace Dvsa\Olcs\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Service\Document\Bookmark\Base\DynamicBookmark;
use Dvsa\Olcs\Api\Domain\Query\Bookmark\UserBundle as Qry;
use Dvsa\Olcs\Api\Domain\Query\Bookmark\LicenceBundle as QryLic;

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
        $licenceBundle = [
            'trafficArea',
        ];

        return [
            Qry::create(['id' => $data['user'], 'bundle' => $bundle]),
            QryLic::create(['id' => $data['licence'], 'bundle' => $licenceBundle])
        ];
    }

    /**
     * Render
     *
     * @return string
     */
    public function render()
    {
        $licData = $this->data[1];
        $userData = $this->data[0];

        $directDial = $this->fetchDirectDial();

        $address = $this->fetchBestAddress();

        $departmentName = (isset($licData['trafficArea']['isNi']) && !$licData['trafficArea']['isNi'])
            ? 'Office of the Traffic Commissioner'
            : '';

        $taName = isset($licData['trafficArea']['name'])
            ? $licData['trafficArea']['name']
            : '';

        $details = $userData['contactDetails'];

        return implode(
            "\n",
            array_filter(
                [
                    Formatter\Name::format($details['person']),
                    $departmentName,
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
        $userData = $this->data[0];

        // we prefer an address directly linked against the user...
        if (!empty($userData['contactDetails']['address'])) {
            return $userData['contactDetails']['address'];
        }

        // but if not, fall back to the one against the team's TA
        return $userData['team']['trafficArea']['contactDetails']['address'];
    }

    /**
     * Fetch Direct Dial
     *
     * @return null|string
     */
    private function fetchDirectDial()
    {
        $userData = $this->data[0];
        if (empty($userData['contactDetails']['phoneContacts'])) {
            return '';
        }
        foreach ($userData['contactDetails']['phoneContacts'] as $phone) {
            if ($phone['phoneContactType']['id'] === self::TEL_DIRECT_DIAL) {
                return $phone['phoneNumber'];
            }
        }
        return null;
    }
}
