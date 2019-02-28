<?php

namespace Dvsa\Olcs\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Domain\Query\Bookmark\IrhpPermitStockBundle as Qry;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;

/**
 * IrhpStartDate
 *
 * @author Henry White <henry.white@capgemini.com>
 */
class IrhpStartDate extends SingleValueAbstract
{
    const FORMATTER = 'DateDayMonthYear';
    const FIELD  = 'validFrom';
    const SRCH_FLD_KEY = 'id';
    const SRCH_VAL_KEY = 'irhpPermitStock';
    const QUERY_CLASS = Qry::class;

    public function render()
    {
        $now = strtotime('today midnight');

        if (isset($this->data['validFrom'])) {
            if ($now > strtotime($this->data['validFrom'])) {
                $this->setData(['validFrom' => date("d F Y", $now)]);
            }
        }

        return parent::render();
    }
}
