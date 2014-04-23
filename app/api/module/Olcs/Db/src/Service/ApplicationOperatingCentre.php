<?php
/**
 * Operating Centre Service
 *  - Takes care of the CRUD actions on Operating Centre entities
 *
 * @author Jess Rowbottom <jess.rowbottom@valtech.co.uk>
 */

namespace Olcs\Db\Service;

use Olcs\Db\Exceptions\NoVersionException;
use Doctrine\DBAL\LockMode;

/**
 * Organisation Service
 *  - Takes care of the CRUD actions Organisation entities
 *
 * @author Jess Rowbottom <jess.rowbottom@valtech.co.uk>
 */
class ApplicationOperatingCentre extends ServiceAbstract
{

    /**
     * Gets an organisation record by licenceId.
     * @todo Possibly use join... needs performence review
     *
     * @param array $options    Array of options, currently only applicationId supported
     *
     * @return array
     */
    public function getByApplicationId($options)
    {
        $this->log(sprintf('Service Executing: \'%1$s\' with \'%2$s\'', __METHOD__, print_r(func_get_args(), true)));

        $sql="SELECT address.*, aoc.no_of_trailers_required, aoc.no_of_vehicles_required,
                aoc.permission,aoc.ad_placed
                FROM application_operating_centre aoc
                LEFT JOIN operating_centre oc ON aoc.operatingCentreId=oc.id
                LEFT JOIN address ON oc.F_Address_UID=address.id
                WHERE aoc.applicationId = ?";
        $dataQuery = $this->em->getConnection()->prepare($sql);
        $dataQuery->bindValue(1,$options['applicationId']);
        $dataQuery->execute();
        $results = $dataQuery->fetchAll();
        return $results;
    }

}
