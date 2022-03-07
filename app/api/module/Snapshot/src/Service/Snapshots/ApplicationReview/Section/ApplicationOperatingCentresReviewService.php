<?php

/**
 * Application Operating Centres Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Snapshot\Service\Snapshots\ApplicationReview\Section;

/**
 * Application Operating Centres Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationOperatingCentresReviewService extends AbstractOperatingCentresReviewService
{
    /**
     * Format the readonly config from the given data
     *
     * @param array $data
     * @return array
     */
    public function getConfigFromData(array $data = [])
    {
        $config = ['subSections' => []];

        $isPsv = $this->isPsv($data);

        if ($isPsv) {
            $ocService = $this->getServiceLocator()->get('Review\PsvOperatingCentre');
            $authService = $this->getServiceLocator()->get('Review\ApplicationPsvOcTotalAuth');
        } else {
            $ocService = $this->getServiceLocator()->get('Review\GoodsOperatingCentre');
            $authService = $this->getServiceLocator()->get('Review\ApplicationGoodsOcTotalAuth');
        }

        $added = [];

        foreach ($data['operatingCentres'] as $operatingCentre) {
            $added[] = $ocService->getConfigFromData($operatingCentre);
        }

        if (!empty($added)) {
            $config['subSections'][] = ['mainItems' => $added];
        }

        $config['subSections'][] = [
            'mainItems' => [
                $this->getServiceLocator()->get('Review\TrafficArea')->getConfigFromData($data),
                $authService->getConfigFromData($data)
            ]
        ];

        return $config;
    }
}
