<?php

namespace Dvsa\Olcs\Api\Service\Submission\Sections;

use Dvsa\Olcs\Api\Entity\Cases\Cases as CasesEntity;
use Dvsa\Olcs\Api\Entity\Prohibition\Prohibition;

/**
 * Class ProhibiitonHistory
 * @package Dvsa\Olcs\Api\Service\Submission\Sections
 * @author Shaun Lizzio <shaun.lizzio@valtech.co.uk>
 */
final class ProhibitionHistory extends AbstractSection
{
    /**
     * Generate only the section data required.
     *
     * @param CasesEntity $case
     * @return array
     */
    public function generateSection(CasesEntity $case)
    {
        $prohibitions = $case->getProhibitions();

        $data = [];
        for ($i=0; $i<count($prohibitions); $i++) {
            /** @var Prohibition $prohibition */
            $prohibition = $prohibitions->current();

            $thisRow = array();
            $thisRow['id'] = $prohibition->getId();
            $thisRow['version'] = $prohibition->getVersion();
            $thisRow['prohibitionDate'] = $prohibition->getProhibitionDate();;
            $thisRow['clearedDate'] = $prohibition->getClearedDate();
            $thisRow['vehicle'] = $prohibition->getVrm();
            $thisRow['trailer'] = $prohibition->getIsTrailer();
            $thisRow['imposedAt'] = $prohibition->getImposedAt();
            $thisRow['prohibitionType'] = $prohibition->getProhibitionType()->getDescription();

            $data[] = $thisRow;

            $prohibitions->next();
        }

        return [
            'data' => [
                'tables' => [
                    'prohibition-history' => $data
                ],
                'text' => $case->getProhibitionNote()
            ]
        ];
    }
}
