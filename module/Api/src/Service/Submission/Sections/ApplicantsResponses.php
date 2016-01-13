<?php

namespace Dvsa\Olcs\Api\Service\Submission\Sections;

use Dvsa\Olcs\Api\Entity\Cases\Cases as CasesEntity;

/**
 * Class ApplicantsResponses
 * @package Dvsa\Olcs\Api\Service\Submission\Sections
 * @author Shaun Lizzio <shaun.lizzio@valtech.co.uk>
 */
final class ApplicantsResponses extends AbstractSection
{
    public function generateSection(CasesEntity $case)
    {
        $defaultText = $this->getViewRenderer()->render('/sections/applicants-responses.phtml');

        return ['data' => ['text' => $defaultText]];
    }
}
