<?php

namespace Dvsa\Olcs\Api\Service\Submission\Sections;

use Dvsa\Olcs\Api\Entity\Cases\Cases as CasesEntity;
use Dvsa\Olcs\Api\Entity\Si\SeriousInfringement as SeriousInfringmentEntity;

/**
 * Class MostSeriousInfringement
 * @package Dvsa\Olcs\Api\Service\Submission\Sections
 * @author Shaun Lizzio <shaun.lizzio@valtech.co.uk>
 */
final class MostSeriousInfringement extends AbstractSection
{
    public function generateSection(CasesEntity $case)
    {
        $seriousInfringements = $case->getSeriousInfringements();
        $data = [];
        $data['id'] = '';
        $data['notificationNumber'] = $case->getErruRequest()->getNotificationNumber();
        $data['siCategory'] = '';
        $data['siCategoryType'] = '';
        $data['infringementDate'] = '';
        $data['checkDate'] = '';
        $data['isMemberState'] = true; /**  @todo do we need this? It will always be true */

        if (isset($seriousInfringements[0]) && $seriousInfringements[0] instanceof SeriousInfringmentEntity) {
            /** @var SeriousInfringmentEntity $mostSeriousInfringement */
            $mostSeriousInfringement = $seriousInfringements[0];
            $data['id'] = $mostSeriousInfringement->getId();
            $data['siCategory'] = !empty($mostSeriousInfringement->getSiCategory()) ?
                $mostSeriousInfringement->getSiCategory()->getDescription() : '';
            $data['siCategoryType'] = !empty($mostSeriousInfringement->getSiCategoryType()) ?
                $mostSeriousInfringement->getSiCategoryType()->getDescription() : '';
            $data['infringementDate'] = $mostSeriousInfringement->getInfringementDate();
            $data['checkDate'] =  $this->formatDate($mostSeriousInfringement->getCheckDate());
        }

        return ['data' => ['overview' => $data]];
    }
}
