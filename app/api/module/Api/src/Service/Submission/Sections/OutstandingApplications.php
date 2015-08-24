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
    public function generateSection(CasesEntity $case, \ArrayObject $context = null)
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
            $data[$i]['receivedDate'] = $applicationEntity->getReceivedDate();
            $data[$i]['oor'] = $applicationEntity->getOutOfRepresentationDate();
            $data[$i]['ooo'] = $applicationEntity->getOutOfOppositionDate();

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

    /**
     * Filter provider
     *
     * @return array
     */
    public function sectionTestProvider()
    {
        $case = $this->getCase();

        $expectedResult = [
            'data' => [
                'tables' => [
                    'people' => [
                        0 => [
                            'id' => 1,
                            'title' => 'title-desc',
                            'forename' => 'fn1',
                            'familyName' => 'sn1',
                            'birthDate' => new \DateTime('1977-01-1')
                        ]
                    ]
                ]
            ]
        ];

        return [
            [$case, $expectedResult],
        ];
    }
}
