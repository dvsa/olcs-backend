<?php

/**
 * Application Psv Oc Total Auth Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Snapshot\Service\Snapshots\ApplicationReview\Section;

use Dvsa\Olcs\Api\Entity\Licence\Licence;

/**
 * Application Psv Oc Total Auth Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationPsvOcTotalAuthReviewService extends AbstractReviewService
{
    private $licenceTypesWithLargeVehicles = [
        Licence::LICENCE_TYPE_STANDARD_NATIONAL,
        Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL
    ];

    private $licenceTypesWithCommunityLicences = [
        Licence::LICENCE_TYPE_RESTRICTED,
        Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL
    ];

    /**
     * Get total auth config
     *
     * @param array $data
     * @return array
     */
    public function getConfigFromData(array $data = array())
    {
        $config = [
            'header' => 'review-operating-centres-authorisation-title',
            'multiItems' => [
                [
                    [
                        'label' => 'review-operating-centres-authorisation-vehicles',
                        'value' => $data['totAuthVehicles']
                    ]
                ]
            ]
        ];

        // @NOTE here we use a slightly different method to modify the config, we REMOVE the large vehicles,
        // everywhere else we generally only add relevant nodes, but due to where the large vehicles node needs to sit
        // it's easier to conditionally remove it, also there is no real processing involved in adding it initially so
        // this isn't really a big deal
        if (!in_array($data['licenceType']['id'], $this->licenceTypesWithLargeVehicles)) {
            unset($config['multiItems'][0]['large']);
        }

        // Conditionally add the community licences, if the licence type is restricted or standard international
        if (in_array($data['licenceType']['id'], $this->licenceTypesWithCommunityLicences)) {
            $config['multiItems'][0][] = [
                'label' => 'review-operating-centres-authorisation-community-licences.psv',
                'value' => $data['totCommunityLicences']
            ];
        }

        return $config;
    }
}
