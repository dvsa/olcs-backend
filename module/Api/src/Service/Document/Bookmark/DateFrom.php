<?php

namespace Dvsa\Olcs\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Service\Document\Bookmark\Base\DynamicBookmark;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Domain\Query\Bookmark\ApplicationBundle as ApplicationQry;
use Dvsa\Olcs\Api\Domain\Query\Bookmark\CommunityLicBundle as CommunityLicQry;

/**
 * Community Licence - Valid From
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class DateFrom extends DynamicBookmark
{
    public function getQuery(array $data)
    {
        $query = [
            CommunityLicQry::create(['id' => $data['communityLic']]),
            ApplicationQry::create(['id' => $data['application'], 'bundle' => ['interimStatus']])
        ];

        return $query;
    }

    public function render()
    {
        if (isset($this->data[1]['interimStatus']['id']) &&
            $this->data[1]['interimStatus']['id'] == Application::INTERIM_STATUS_INFORCE) {

            if ($this->data[1]['interimStart'] === null) {
                return null;
            }

            return $this->data[1]['interimStart']->format('d/m/Y');
        }

        if ($this->data[0]['specifiedDate'] === null) {
            return null;
        }

        return $this->data[0]['specifiedDate']->format('d/m/Y');
    }
}
