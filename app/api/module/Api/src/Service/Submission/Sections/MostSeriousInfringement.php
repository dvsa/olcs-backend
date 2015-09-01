<?php

namespace Dvsa\Olcs\Api\Service\Submission\Sections;

use Dvsa\Olcs\Api\Entity\Cases\Cases as CasesEntity;
use Dvsa\Olcs\Api\Entity\Si\SeriousInfringement as SeriousInfringmentEntity;
use Dvsa\Olcs\Api\Service\Submission\Sections\AbstractSection;

/**
 * Class MostSeriousInfringement
 * @package Dvsa\Olcs\Api\Service\Submission\Sections
 * @author Shaun Lizzio <shaun.lizzio@valtech.co.uk>
 */
final class MostSeriousInfringement extends AbstractSection
{
    public function generateSection(CasesEntity $case, \ArrayObject $context = null)
    {
        $seriousInfringements = $case->getSeriousInfringements();
        $mostSeriousInfringement = [];
        if (isset($seriousInfringements[0])) {
            /** @var SeriousInfringmentEntity $mostSeriousInfringement */
            $mostSeriousInfringement = $seriousInfringements[0];
        }

        $data['id'] = !empty($mostSeriousInfringement) ? $mostSeriousInfringement->getId() : '';
        $data['notificationNumber'] = !empty($mostSeriousInfringement) ?
            $mostSeriousInfringement->getNotificationNumber() : '';
        $data['siCategory'] = !empty($mostSeriousInfringement) ?
            $mostSeriousInfringement->getSiCategory()->getDescription() : '';
        $data['siCategoryType'] = !empty($mostSeriousInfringement) ?
            $mostSeriousInfringement->getSiCategoryType()->getDescription() : '';
        $data['infringementDate'] = !empty($mostSeriousInfringement) ?
            $mostSeriousInfringement->getInfringementDate() : '';
        $data['checkDate'] = !empty($mostSeriousInfringement) ? $mostSeriousInfringement->getCheckDate() : '';
        $data['isMemberState'] = !empty($mostSeriousInfringement) ?
            $mostSeriousInfringement->getMemberStateCode()->getIsMemberState() : '';

        return ['data' => ['overview' => $data]];
    }
}
