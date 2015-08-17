<?php

namespace Dvsa\Olcs\Api\Domain\Query\Bookmark;

use Dvsa\Olcs\Transfer\Query\AbstractQuery;

/**
 * FStandingAdditionalVeh
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class FStandingAdditionalVeh extends AbstractQuery
{
    protected $goodsOrPsv;

    protected $licenceType;

    protected $effectiveFrom;

    /**
     * @return mixed
     */
    public function getGoodsOrPsv()
    {
        return $this->goodsOrPsv;
    }

    /**
     * @return mixed
     */
    public function getLicenceType()
    {
        return $this->licenceType;
    }

    /**
     * @return mixed
     */
    public function getEffectiveFrom()
    {
        return $this->effectiveFrom;
    }
}
