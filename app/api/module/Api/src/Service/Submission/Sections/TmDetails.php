<?php

namespace Dvsa\Olcs\Api\Service\Submission\Sections;

use Dvsa\Olcs\Api\Entity\Cases\Cases as CasesEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;

/**
 * Class TmDetails
 * @package Dvsa\Olcs\Api\Service\Submission\Section\TmDetails
 * @author Shaun Lizzio <shaun.lizzio@valtech.co.uk>
 */
final class TmDetails extends AbstractSection
{
    public function generateSection(CasesEntity $case)
    {
        $data = array();
        $data['id'] = '';
        $data['title'] = '';
        $data['forename'] = '';
        $data['familyName'] = '';
        $data['emailAddress'] = '';
        $data['dob'] = '';
        $data['placeOfBirth'] = '';
        $data['tmType'] = '';
        $data['homeAddress'] = '';
        $data['workAddress'] = '';

        if (!empty($case->getTransportManager())) {
            $tmData = $case->getTransportManager();
            $person = $this->extractPerson($tmData->getHomeCd());

            $data['id'] = $tmData->getId();
            $data['title'] = $person['title'];
            $data['forename'] = $person['forename'];
            $data['familyName'] = $person['familyName'];
            $data['emailAddress'] = $tmData->getHomeCd()->getEmailAddress();
            $data['dob'] = $this->formatDate($person['birthDate']);
            $data['placeOfBirth'] = $person['birthPlace'];
            $data['tmType'] = !empty($tmData->getTmType()) ? $tmData->getTmType()->getDescription() : '';
            $data['homeAddress'] = !empty($tmData->getHomeCd()->getAddress()) ?
                $tmData->getHomeCd()->getAddress()->toArray() : [];
            $data['workAddress'] = !empty($tmData->getWorkCd()->getAddress()) ?
                $tmData->getWorkCd()->getAddress()->toArray() : [];
        }

        return ['data' => ['overview' => $data]];
    }
}
