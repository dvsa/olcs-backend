<?php

/**
 * Trading Names Service
 *
 * @author Jakub Igla <jakub.igla@valtech.co.uk>
 */

namespace Olcs\Db\Service;

use Olcs\Db\Exceptions\NoVersionException;
use Doctrine\DBAL\LockMode;

/**
 * Trading Names Service
 *
 * @author Jakub Igla <jakub.igla@valtech.co.uk>
 */
class TradingName extends ServiceAbstract
{

    /**
     * Remove all trading names based on licence Id
     *
     * @param $licenceId
     * @return void
     */
    public function removeAll($licenceId)
    {
        /**
         * @todo Soft delete functionality
         */
        $q = $this->getEntityManager()->createQuery('delete from OlcsEntities\Entity\TradingName tm where tm.licence = ' . $licenceId);
        $q->execute();
    }

}
