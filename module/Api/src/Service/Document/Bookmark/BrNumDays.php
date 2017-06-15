<?php

namespace Dvsa\Olcs\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Domain\Query\Bookmark\BusRegBundle as Qry;
use Dvsa\Olcs\Api\Service\Document\Bookmark\Base\DynamicBookmark;
use Dvsa\Olcs\Api\Entity\Bus\BusReg as BusRegEntity;

/**
 * Class BrNumDays
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class BrNumDays extends SingleValueAbstract
{
    const SRCH_FLD_KEY = 'id';
    const SRCH_VAL_KEY = DynamicBookmark::PARAM_BUSREG_ID;
    const BUNDLE = ['busNoticePeriod', 'status'];
    const QUERY_CLASS = Qry::class;

    /**
     * Render bookmark
     *
     * @return string
     */
    public function render()
    {
        $period = 'standardPeriod';

        if ($this->data['status']['id'] === BusRegEntity::STATUS_CANCEL) {
            $period = 'cancellationPeriod';
        }

        return $this->data['busNoticePeriod'][$period];
    }
}
