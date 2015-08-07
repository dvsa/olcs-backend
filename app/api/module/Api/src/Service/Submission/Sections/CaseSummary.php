<?php

namespace Dvsa\Olcs\Api\Service\Submission\Sections;

use Dvsa\Olcs\Api\Entity\Cases\Cases as CasesEntity;
use Dvsa\Olcs\Api\Service\Submission\Sections\AbstractSection;

/**
 * Class CaseSummary
 * @package Dvsa\Olcs\Api\Service\Submission\Section\CaseSummary
 * @author Shaun Lizzio <shaun.lizzio@valtech.co.uk>
 */
final class CaseSummary extends AbstractSection
{
    public function generateSection(CasesEntity $case, \ArrayObject $context)
    {
        $data = [
            'caseType' => $case->getCaseType()->getDescription()
        ];

        $context->offsetSet('case-summary', $data);

        return $context;
    }
}
