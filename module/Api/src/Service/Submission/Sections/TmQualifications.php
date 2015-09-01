<?php

namespace Dvsa\Olcs\Api\Service\Submission\Sections;

use Dvsa\Olcs\Api\Entity\Cases\Cases as CasesEntity;
use Dvsa\Olcs\Api\Entity\Tm\TmQualification;
use Dvsa\Olcs\Api\Entity\Tm\TransportManagerApplication;
use Dvsa\Olcs\Api\Entity\Tm\TransportManagerLicence;

/**
 * Class TmQualifications
 * @package Dvsa\Olcs\Api\Service\Submission\Sections
 * @author Shaun Lizzio <shaun.lizzio@valtech.co.uk>
 */
final class TmQualifications extends AbstractSection
{
    /**
     * Generate only the section data required.
     *
     * @param CasesEntity $case
     * @return array
     */
    public function generateSection(CasesEntity $case)
    {
        $data = [];

        if (!empty($case->getTransportManager())) {

            $tmQualifications = $case->getTransportManager()->getQualifications();

            for ($i = 0; $i < count($tmQualifications); $i++) {
                /** @var TmQualification $entity */
                $entity = $tmQualifications->current();

                $thisRow = array();
                $thisRow['id'] = $entity->getId();
                $thisRow['version'] = $entity->getVersion();
                $thisRow['qualificationType'] = $entity->getQualificationType()->getDescription();
                $thisRow['serialNo'] = $entity->getSerialNo();
                $thisRow['issuedDate'] = $entity->getIssuedDate();
                $thisRow['country'] = $entity->getCountryCode()->getCountryDesc();

                $data[] = $thisRow;

                $tmQualifications->next();
            }
        }

        return [
            'data' => [
                'tables' => [
                    'tm-qualifications' => $data,
                ]
            ]
        ];
    }
}
