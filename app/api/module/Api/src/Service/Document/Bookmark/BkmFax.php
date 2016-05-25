<?php

namespace Dvsa\Olcs\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Domain\Query\Bookmark\OrganisationBundle as Qry;
use Dvsa\Olcs\Api\Entity\ContactDetails\PhoneContact as PhoneContactEntity;
use Dvsa\Olcs\Api\Service\Document\Bookmark\Base\DynamicBookmark;

/**
 * BkmFax
 */
class BkmFax extends DynamicBookmark
{
    /**
     * Gets query to retrieve data
     *
     * @param array $data
     * @return Qry|null
     */
    public function getQuery(array $data)
    {
        return Qry::create(
            [
                'id' => $data['organisation'],
                'bundle' => [
                    'irfoContactDetails' => [
                        'phoneContacts' => [
                            'phoneContactType'
                        ],
                    ]
                ]
            ]
        );
    }

    /**
     * Renders the bookmark
     *
     * @return string
     */
    public function render()
    {
        if (empty($this->data['irfoContactDetails']['phoneContacts'])) {
            return '';
        }

        foreach ($this->data['irfoContactDetails']['phoneContacts'] as $phoneContact) {
            if (!empty($phoneContact['phoneContactType']['id'])
                && ($phoneContact['phoneContactType']['id'] === PhoneContactEntity::TYPE_FAX)
            ) {
                return $phoneContact['phoneNumber'];
            }
        }

        return '';
    }
}
