<?php

namespace Dvsa\Olcs\Snapshot\Service\Snapshots\Surrender\Section;

use Dvsa\Olcs\Api\Entity\Surrender;

class CurrentDiscsReviewService extends AbstractReviewService
{
    /**
     * @param Surrender $surrender
     *
     * @return array
     */
    public function getConfigFromData(Surrender $surrender)
    {
        $items = [];

        $items[] =
            [
                'label' => 'surrender-review-current-discs-destroyed',
                'value' => $surrender->getDiscDestroyed() === null ? 0 : $surrender->getDiscDestroyed()
            ];


        $items[] =
            [
                'label' => 'surrender-review-current-discs-lost',
                'value' => $surrender->getDiscLost() === null ? 0 : $surrender->getDiscLost()
            ];

        if ($surrender->getDiscLost() !== null) {
            $items[] =
                [
                    'label' => 'surrender-review-additional-information',
                    'value' => $surrender->getDiscLostInfo()
                ];
        }

        $items[] = [

            'label' => 'surrender-review-current-discs-stolen',
            'value' => $surrender->getDiscStolen() === null ? 0 : $surrender->getDiscStolen()

        ];

        if ($surrender->getDiscStolen() !== null) {
            $items[] = [

                'label' => 'surrender-review-additional-information',
                'value' => $surrender->getDiscStolenInfo()

            ];
        }

        return [
            'multiItems' => [
                $items
            ]
        ];
    }
}

