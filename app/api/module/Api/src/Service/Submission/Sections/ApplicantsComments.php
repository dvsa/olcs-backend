<?php

namespace Dvsa\Olcs\Api\Service\Submission\Sections;

use Dvsa\Olcs\Api\Entity\Cases\Cases as CasesEntity;

/**
 * Class ApplicantsComments
 * @package Dvsa\Olcs\Api\Service\Submission\Sections
 * @author Shaun Lizzio <shaun.lizzio@valtech.co.uk>
 */
final class ApplicantsComments extends AbstractSection
{
    public function generateSection(CasesEntity $case)
    {
        $defaultText = "<h2>Applicant/Operator responses</h2>
<h3>Hours of Operation:</h3>
<p>Monday to Friday:<br />
Saturday:<br />
Sunday:<br />
Bank Holiday:<br /></p>
<h3>Hours of Maintenance:</h3>
<p>Monday to Friday:<br />
Saturday:<br />
Sunday:<br />
Bank Holiday:<br /></p>
<h2>Applicant/Operator comments</h2>
<p>TE REPORT:</p>
<p>SIZE:</p>
<p>ACCESS/EGRESS/MANOEUVRE:</p>
<p>VISIBILITY:</p>
<p>TE COMMENTS:</p>
<p>TE CONCLUSIONS:</p>";

        return ['data' => ['text' => $defaultText]];
    }
}
