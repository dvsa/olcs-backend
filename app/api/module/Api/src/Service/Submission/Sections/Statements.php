<?php

namespace Dvsa\Olcs\Api\Service\Submission\Sections;

use Dvsa\Olcs\Api\Entity\Cases\Cases as CasesEntity;
use Dvsa\Olcs\Api\Entity\Cases\Statement;

/**
 * Class ProhibiitonHistory
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
        for ($i = 0; $i < count($statements); $i++) {
            /** @var Statement $entity */
            $entity = $statements->current();

            $thisRow = array();
            $thisRow['id'] = $entity->getId();
            $thisRow['version'] = $entity->getVersion();
            $thisRow['requestedDate'] = $entity->getRequestedDate()->format('d/m/Y');
            $thisRow['requestedBy'] = $this->extractPerson($entity->getRequestorsContactDetails());
            $thisRow['statementType'] = $entity->getStatementType()->getDescription();
            $thisRow['stoppedDate'] = $entity->getStoppedDate();
            $thisRow['requestorsBody'] = $entity->getRequestorsBody();
            $thisRow['issuedDate'] = $entity->getIssuedDate();
            $thisRow['vrm'] = $entity->getVrm();

            $data[] = $thisRow;

            $statements->next();
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
