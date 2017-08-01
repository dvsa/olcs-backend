<?php

namespace Dvsa\Olcs\Snapshot\Service\Snapshots\ContinuationReview\Section;

use Dvsa\Olcs\Api\Entity\Licence\ContinuationDetail;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Licence\Workshop;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails;
use Dvsa\Olcs\Api\Entity\ContactDetails\Address;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Safety Continuation Review Service
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class SafetyReviewService extends AbstractReviewService
{
    /**
     * Format the readonly config from the given data
     *
     * @param ContinuationDetail $continuationDetail continuation detail
     *
     * @return array
     */
    public function getConfigFromData(ContinuationDetail $continuationDetail)
    {
        /** @var Licence $licence */
        $licence = $continuationDetail->getLicence();
        $workshops = $licence->getWorkshops();

        $header =[
            [
                ['value' => 'continuations.safety-section.table.inspector', 'header' => true],
                ['value' => 'continuations.safety-section.table.address', 'header' => true],
            ]
        ];

        $config = [];
        /** @var Workshop $workshop */
        foreach ($workshops as $workshop) {
            /** @var ContactDetails $contactDetails */
            $contactDetails = $workshop->getContactDetails();
            /** @var Address $address */
            $address = $contactDetails->getAddress();
            $row = [
                [
                    'value' => $contactDetails->getFao()
                        . ' ('
                        . (($workshop->getIsExternal() === 'Y')
                            ? $this->translate('continuations.safety-section.table.external-contractor')
                            : $this->translate('continuations.safety-section.table.owner-or-employee'))
                        . ')',
                ],
                ['value' => implode(', ', [$address->getAddressLine1(), $address->getTown()])]
            ];
            $config[] = $row;

        }
        usort(
            $config,
            function ($a, $b) {
                return strcmp($a[0]['value'], $b[0]['value']);
            }
        );

        return array_merge($header, $config);
    }
}
