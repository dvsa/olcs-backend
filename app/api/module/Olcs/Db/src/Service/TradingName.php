<?php

/**
 * Trading Names Service
 *
 * @author Jakub Igla <jakub.igla@valtech.co.uk>
 */
namespace Olcs\Db\Service;

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
        $query = $this->getEntityManager()->createQuery(
            'delete from Olcs\Db\Entity\TradingName tm where tm.licence = ' . $licenceId
        );
        $query->execute();
    }
}
