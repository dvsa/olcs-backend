<?php

/**
 * TradingName Service
 *  - Takes care of the CRUD actions TradingName entities
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Olcs\Db\Service;

/**
 * TradingName Service
 *  - Takes care of the CRUD actions TradingName entities
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TradingName extends ServiceAbstract
{

    /**
     * Returns an indexed array of valid search terms for this service / entity.
     *
     * @return array
     */
    public function getValidSearchFields()
    {
        return array();
    }

}
