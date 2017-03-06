<?php

namespace Dvsa\Olcs\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Service\Document\Bookmark\Base\DynamicBookmark;
use Dvsa\Olcs\Api\Domain\Query\Bookmark\BusRegBundle as Qry;
use Dvsa\Olcs\Api\Entity\Bus\BusReg;

/**
 * BrRegVarOrCanc Bookmark
 */
class BrRegVarOrCanc extends DynamicBookmark
{
    protected $params = [DynamicBookmark::PARAM_BUSREG_ID];

    /**
     * Get the query to retrieve date required by the bookmark
     *
     * @param array $data Data
     *
     * @return Qry
     */
    public function getQuery(array $data)
    {
        return Qry::create(['id' => $data[DynamicBookmark::PARAM_BUSREG_ID]]);
    }

    /**
     * Render the bookmark
     *
     * @return string
     * @throws \Exception
     */
    public function render()
    {
        if (!empty($this->data['status']['id'])) {
            switch ($this->data['status']['id']) {
                case BusReg::STATUS_NEW:
                    return 'commence';
                case BusReg::STATUS_VAR:
                    return 'vary';
                case BusReg::STATUS_CANCEL:
                    return 'cancel';
            }
        }

        throw new \Exception('Failed to generate bookmark '. __CLASS__);
    }
}
