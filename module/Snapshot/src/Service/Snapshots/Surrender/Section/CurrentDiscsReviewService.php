<?php

namespace Dvsa\Olcs\Snapshot\Service\Snapshots\Surrender\Section;

use Dvsa\Olcs\Api\Entity\Surrender;

class CurrentDiscsReviewService extends AbstractReviewService
{
    /**
     * @param Surrender $surrender
     * @return array
     */
    public function getConfigFromData(Surrender $surrender)
    {
        return [
            'multiItems' => [
                [
                    [
                        'label' => 'surrender-review-current-discs-destroyed',
                        'value' => $surrender->getDiscDestroyed()
                    ],
                    [
                        'label' => 'surrender-review-current-discs-lost',
                        'value' => $surrender->getDiscLost()
                    ],
                    [
                        'label' => 'surrender-review-additional-information',
                        'value' => $surrender->getDiscLostInfo()
                    ],
                    [
                        'label' => 'surrender-review-current-discs-stolen',
                        'value' => $surrender->getDiscStolen()
                    ],
                    [
                        'label' => 'surrender-review-additional-information',
                        'value' => $surrender->getDiscStolenInfo()
                    ]
                ]
            ]
        ];
    }

}
