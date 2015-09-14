<?php

namespace Dvsa\Olcs\Api\Service\Submission\Sections;

use Dvsa\Olcs\Api\Entity\Cases\Cases as CasesEntity;
use Dvsa\Olcs\Api\Entity\Cases\Statement;

/**
 * Class Statements
 * @package Dvsa\Olcs\Api\Service\Submission\Sections
 * @author Shaun Lizzio <shaun.lizzio@valtech.co.uk>
 */
final class Statements extends AbstractSection
{
    /**
     * Generate only the section data required.
     *
     * @param CasesEntity $case
     * @return array
     */
    public function generateSection(CasesEntity $case)
    {
        $statements = $case->getStatements();

        $data = [];

        /** @var Statement $entity */
        foreach ($statements as $entity) {
            $thisRow = array();
            $thisRow['id'] = $entity->getId();
            $thisRow['version'] = $entity->getVersion();
            $thisRow['requestedDate'] = $this->formatDate($entity->getRequestedDate());
            $thisRow['requestedBy'] = $this->extractPerson($entity->getRequestorsContactDetails());
            $thisRow['statementType'] = $entity->getStatementType()->getDescription();
            $thisRow['stoppedDate'] = $this->formatDate($entity->getStoppedDate());
            $thisRow['requestorsBody'] = $entity->getRequestorsBody();
            $thisRow['issuedDate'] = $this->formatDate($entity->getIssuedDate());
            $thisRow['vrm'] = $entity->getVrm();

            $data[] = $thisRow;
        }

        return [
            'data' => [
                'tables' => [
                    'statements' => $data
                ]
            ]
        ];
    }
}
