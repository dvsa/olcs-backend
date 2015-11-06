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
        $defaultText = "<h3>Hours of Operation:</h3>
<p>Monday to Friday:<br />
Saturday:<br />
Sunday:<br />
Bank Holiday:<br /></p>
<h3>Hours of Maintenance:</h3>
<p>Monday to Friday:<br />
Saturday:<br />
Sunday:<br />
Bank Holiday:<br /></p>";

        return ['data' => ['text' => $defaultText]];
    }
}
