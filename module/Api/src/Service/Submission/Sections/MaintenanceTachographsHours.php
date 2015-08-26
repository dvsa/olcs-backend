<?php

namespace Dvsa\Olcs\Api\Service\Submission\Sections;

use Dvsa\Olcs\Api\Entity\Cases\Cases as CasesEntity;

/**
 * Class MaintenanceTachographsHours
 * @package Dvsa\Olcs\Api\Service\Submission\Sections
 * @author Shaun Lizzio <shaun.lizzio@valtech.co.uk>
 */
final class MaintenanceTachographsHours extends AbstractSection
{
    public function generateSection(CasesEntity $case)
    {
        return ['data' => []];
    }
}
