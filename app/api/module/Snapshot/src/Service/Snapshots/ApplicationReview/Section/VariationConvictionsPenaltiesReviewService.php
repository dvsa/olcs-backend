<?php

/**
 * Variation Convictions Penalties Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Snapshot\Service\Snapshots\ApplicationReview\Section;

/**
 * Variation Convictions Penalties Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationConvictionsPenaltiesReviewService extends AbstractReviewService
{
    /**
     * Format the readonly config from the given data
     *
     * @param array $data
     * @return array
     */
    public function getConfigFromData(array $data = array())
    {
        return $this->getServiceLocator()->get('Review\ApplicationConvictionsPenalties')->getConfigFromData($data);
    }
}
