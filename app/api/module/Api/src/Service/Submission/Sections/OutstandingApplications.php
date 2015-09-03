<?php

namespace Dvsa\Olcs\Api\Service\Submission\Sections;

use Dvsa\Olcs\Api\Entity\Cases\Cases as CasesEntity;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;

/**
 * Class OutstandingApplications
 * @package Dvsa\Olcs\Api\Service\Submission\Sections
 * @author Shaun Lizzio <shaun.lizzio@valtech.co.uk>
 */
final class OutstandingApplications extends AbstractSection
{
    public function generateSection(CasesEntity $case)
    {
        $licence = $case->getLicence();
        $organisation = !empty($licence) ? $licence->getOrganisation() : '';
        $outstandingApplications = !empty($organisation) ? $organisation->getOutstandingApplications() : [];

        $data = [];
        
        for ($i=0; $i<count($outstandingApplications); $i++) {
            /** @var ApplicationEntity $applicationEntity */
            $applicationEntity = $outstandingApplications->current();

            $data[$i]['id'] = $applicationEntity->getId();
            $data[$i]['version'] = $applicationEntity->getVersion();
            $data[$i]['applicationType'] = 'TBC';
            $data[$i]['receivedDate'] = $this->formatDate($applicationEntity->getReceivedDate());
            $data[$i]['oor'] = $applicationEntity->getOutOfRepresentationDateAsString();
            $data[$i]['ooo'] = $applicationEntity->getOutOfOppositionDateAsString();

            $outstandingApplications->next();
        }

        return [
            'data' => [
                'tables' => [
                    'outstanding-applications' => $data
                ]
            ]
        ];
    }
}
