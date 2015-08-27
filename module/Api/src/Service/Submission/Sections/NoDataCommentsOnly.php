<?php

namespace Dvsa\Olcs\Api\Service\Submission\Sections;

use Dvsa\Olcs\Api\Entity\Cases\Cases as CasesEntity;

/**
 * Class NoDataCommentsOnly
 * @package Dvsa\Olcs\Api\Service\Submission\Sections
 * @author Shaun Lizzio <shaun.lizzio@valtech.co.uk>
 */
final class NoDataCommentsOnly extends AbstractSection
{
    /**
     * Generate only the section data required.
     *
     * @param CasesEntity $case
     * @return array
     */
    public function generateSection(CasesEntity $case)
    {
        return ['data' => []];
    }
}
