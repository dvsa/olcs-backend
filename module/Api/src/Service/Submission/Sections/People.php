<?php

namespace Dvsa\Olcs\Api\Service\Submission\Sections;

use Dvsa\Olcs\Api\Entity\Cases\Cases as CasesEntity;
use Dvsa\Olcs\Api\Entity\Person\Person as PersonEntity;

/**
 * Class People
 * @package Dvsa\Olcs\Api\Service\Submission\Sections
 * @author Shaun Lizzio <shaun.lizzio@valtech.co.uk>
 */
final class People extends AbstractSection
{
    public function generateSection(CasesEntity $case)
    {
        $licence = $case->getLicence();
        $organisation = !empty($licence) ? $licence->getOrganisation() : '';

        $persons = !empty($organisation) ? $organisation->getOrganisationPersons() : [];

        $data = [];
        for ($i=0; $i<count($persons); $i++) {
            /** @var PersonEntity $personEntity */
            $personEntity = $persons->current()->getPerson();

            $data[$i]['id'] = $personEntity->getId();
            $data[$i]['title'] = '';
            if ($personEntity->getTitle() instanceof RefData) {
                $data[$i]['title'] = $personEntity->getTitle()->getDescription();
            }
            $data[$i]['familyName'] = $personEntity->getFamilyName();
            $data[$i]['forename'] = $personEntity->getForename();
            $data[$i]['disqualificationStatus'] = $personEntity->getDisqualificationStatus();
            $data[$i]['birthDate'] = $this->formatDate($personEntity->getBirthDate());

            $persons->next();
        }

        return [
            'data' => [
                'tables' => [
                    'people' => $data
                ]
            ]
        ];
    }
}
