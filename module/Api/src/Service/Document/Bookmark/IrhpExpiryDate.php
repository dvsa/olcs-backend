<?php

namespace Dvsa\Olcs\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Domain\Query\Bookmark\IrhpPermitBundle as Qry;

/**
 * IrhpExpiryDate
 *
 * @author Henry White <henry.white@capgemini.com>
 */
class IrhpExpiryDate extends SingleValueAbstract
{
    const FORMATTER = 'DateDayMonthYear';
    const SRCH_FLD_KEY = 'id';
    const SRCH_VAL_KEY = 'irhpPermit';
    const QUERY_CLASS = Qry::class;
    const BUNDLE = [
        'irhpPermitRange' => [
            'irhpPermitStock' => [
                'validTo'
            ],
        ],
    ];

    /**
     * get value
     *
     * @return string
     */
    protected function getValue()
    {
        return $this->data['expiryDate'] ?? $this->data['irhpPermitRange']['irhpPermitStock']['validTo'] ?? null;
    }
}
