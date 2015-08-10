<?php

namespace Dvsa\Olcs\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Service\Document\Bookmark\Base\DynamicBookmark;
use Dvsa\Olcs\Api\Service\Document\Bookmark\Interfaces\DateHelperAwareInterface;
use Dvsa\Olcs\Api\Service\Document\Bookmark\Traits\DateHelperAwareTrait;
use Dvsa\Olcs\Api\Domain\Query\Bookmark\FStandingAdditionalVeh as Qry;

/**
 * F_Standing_AdditionalVeh bookmark
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class FStandingAdditionalVeh extends DynamicBookmark implements DateHelperAwareInterface
{
    use DateHelperAwareTrait;

    public function getQuery(array $data)
    {
        return Qry::create(
            [
                'goodsOrPsv' => $data['goodsOrPsv'],
                'licenceType' => $data['licenceType'],
                'effectiveFrom' => $this->getDateHelper()->getDate('Y-m-d')
            ]
        );
    }

    public function render()
    {
        if (isset($this->data['Results'][0]['additionalVehicleRate'])) {
            return number_format($this->data['Results'][0]['additionalVehicleRate']);
        }
        return '';
    }
}
