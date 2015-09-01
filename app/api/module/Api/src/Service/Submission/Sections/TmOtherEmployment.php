<?php

namespace Dvsa\Olcs\Api\Service\Submission\Sections;

use Dvsa\Olcs\Api\Entity\Cases\Cases as CasesEntity;
use Dvsa\Olcs\Api\Entity\Tm\TmEmployment;
use Dvsa\Olcs\Api\Entity\Tm\TmQualification;

/**
 * Class TmOtherEmployment
 * @package Dvsa\Olcs\Api\Service\Submission\Sections
 * @author Shaun Lizzio <shaun.lizzio@valtech.co.uk>
 */
final class TmOtherEmployment extends AbstractSection
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

        if (!empty($case->getTransportManager()->getEmployments())) {

            $tmEmployments = $case->getTransportManager()->getEmployments();

            for ($i = 0; $i < count($tmEmployments); $i++) {
                /** @var TmEmployment $entity */
                $entity = $tmEmployments->current();

                $thisRow = array();
                $thisRow['id'] = $entity->getId();
                $thisRow['version'] = $entity->getVersion();
                $thisRow['position'] = $entity->getPosition();
                $thisRow['employerName'] = $entity->getEmployerName();
                $thisRow['address'] = $entity->getContactDetails()->getAddress();
                $thisRow['hoursPerWeek'] = $entity->getHoursPerWeek();

                $data[] = $thisRow;

                $tmEmployments->next();
            }
        }

        return [
            'data' => [
                'tables' => [
                    'tm-other-employment' => $data,
                ]
            ]
        ];
    }
}
