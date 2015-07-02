<?php

/**
 * Vehicles Psv Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Snapshot\Service\Snapshots\ApplicationReview\Section;

use Dvsa\Olcs\Api\Entity\Vehicle\Vehicle;

/**
 * Vehicles Psv Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VehiclesPsvReviewService extends AbstractReviewService
{
    /**
     * Format the readonly config from the given data
     *
     * @param array $data
     * @return array
     */
    public function getConfigFromData(array $data = array(), $mainItems = array())
    {
        if ($data['hasEnteredReg'] == 'Y') {
            $vehicles = $this->splitVehiclesUp($data['licenceVehicles']);

            if (!empty($vehicles[Vehicle::PSV_TYPE_SMALL])) {
                $mainItems[] = $this->formatSmallVehicles($vehicles[Vehicle::PSV_TYPE_SMALL]);
            }

            if (!empty($vehicles[Vehicle::PSV_TYPE_MEDIUM])) {
                $mainItems[] = $this->formatVehicles($vehicles[Vehicle::PSV_TYPE_MEDIUM], 'medium');
            }

            if (!empty($vehicles[Vehicle::PSV_TYPE_LARGE])) {
                $mainItems[] = $this->formatVehicles($vehicles[Vehicle::PSV_TYPE_LARGE], 'large');
            }
        }

        return $mainItems;
    }

    private function formatSmallVehicles($licenceVehicles)
    {
        $smallItems = [];

        foreach ($licenceVehicles as $licenceVehicle) {
            $smallItems[] = [
                [
                    'label' => 'application-review-vehicles-vrm',
                    'value' => $licenceVehicle['vehicle']['vrm']
                ],
                [
                    'label' => 'application-review-vehicles-make',
                    'value' => $licenceVehicle['vehicle']['makeModel'] . $this->maybeIsNovelty($licenceVehicle)
                ]
            ];
        }

        return [
            'header' => 'application-review-vehicles-psv-small-title',
            'multiItems' => $smallItems
        ];
    }

    private function formatVehicles($licenceVehicles, $which)
    {
        $items = [];

        foreach ($licenceVehicles as $licenceVehicle) {
            $items[] = [
                [
                    'label' => 'application-review-vehicles-vrm',
                    'value' => $licenceVehicle['vehicle']['vrm']
                ]
            ];
        }

        return [
            'header' => 'application-review-vehicles-psv-' . $which . '-title',
            'multiItems' => $items
        ];
    }

    private function maybeIsNovelty($licenceVehicle)
    {
        if ($licenceVehicle['vehicle']['isNovelty'] === 'Y') {
            return ' (' . $this->translate('application-review-vehicles-is-novelty') . ')';
        }
    }

    private function splitVehiclesUp($licenceVehicles)
    {
        $vehicles = [
            Vehicle::PSV_TYPE_SMALL => [],
            Vehicle::PSV_TYPE_MEDIUM => [],
            Vehicle::PSV_TYPE_LARGE => []
        ];

        foreach (array_keys($vehicles) as $type) {
            foreach ($licenceVehicles as $licenceVehicle) {
                if ($licenceVehicle['vehicle']['psvType']['id'] == $type) {
                    $vehicles[$type][] = $licenceVehicle;
                }
            }
        }

        return $vehicles;
    }
}
