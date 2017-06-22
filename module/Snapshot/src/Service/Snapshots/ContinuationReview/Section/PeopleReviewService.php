<?php

namespace Dvsa\Olcs\Snapshot\Service\Snapshots\ContinuationReview\Section;

use Dvsa\Olcs\Api\Entity\Licence\ContinuationDetail;
use Dvsa\Olcs\Api\Entity\Organisation\OrganisationPerson;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * People Continuation Review Service
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class PeopleReviewService extends AbstractReviewService
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
        $organisationPersons = $continuationDetail->getLicence()->getOrganisation()->getOrganisationPersons();

        $header[] = [
            ['value' => 'continuations.people-section.table.name', 'header' => true],
            ['value' => 'continuations.people-section.table.date-of-birth', 'header' => true]
        ];
        $config = [];
        /** @var OrganisationPerson $op */
        foreach ($organisationPersons as $op) {
            $person = $op->getPerson();
            $config[] = [
                [
                    'value' => implode(
                        ' ',
                        [
                            $person->getTitle()->getDescription(),
                            $person->getForename(),
                            $person->getFamilyName(),
                        ]
                    )
                ],
                [
                    'value' => $person->getBirthDate(true)->format('d/m/Y')
                ]
            ];
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
