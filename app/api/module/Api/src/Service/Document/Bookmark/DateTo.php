<?php

namespace Dvsa\Olcs\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Service\Document\Bookmark\Base\DynamicBookmark;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Domain\Query\Bookmark\ApplicationBundle as ApplicationQry;
use Dvsa\Olcs\Api\Domain\Query\Bookmark\CommunityLicBundle as CommunityLicQry;

/**
 * Community Licence - Valid To
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class DateTo extends DynamicBookmark
{
    public function getQuery(array $data)
    {
        $query = [
            CommunityLicQry::create(['id' => $data['communityLic'], 'bundle' => ['licence']]),
            ApplicationQry::create(['id' => $data['application'], 'bundle' => ['interimStatus']])
        ];

        return $query;
    }

    public function render()
    {
        if (isset($this->data[1]['interimStatus']['id']) &&
            $this->data[1]['interimStatus']['id'] == Application::INTERIM_STATUS_INFORCE) {
            $dateFrom = date('d/m/Y', strtotime($this->data[1]['interimEnd']));
        } else {
            $dateFrom = date("d/m/Y", strtotime($this->data[0]['licence']['expiryDate']));
        }
        return $dateFrom;
    }
}
