<?php

/**
 * Variation Type Of Licence Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Snapshot\Service\Snapshots\ApplicationReview\Section;

/**
 * Variation Type Of Licence Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationTypeOfLicenceReviewService extends AbstractReviewService
{
    /**
     * Format the readonly config from the given data
     *
     * @param array $data
     * @return array
     */
    public function getConfigFromData(array $data = array())
    {
        return ['freetext' => $this->getFreeText($data)];
    }

    private function getFreeText($data)
    {
        return $this->translateReplace(
            'variation-application-type-of-licence-freetext',
            [
                $this->formatRefdata($data['licence']['licenceType']),
                $this->formatRefdata($data['licenceType'])
            ]
        );
    }
}
