<?php

/**
 * LicenceVehicle Service
 *  - Takes care of the CRUD actions LicenceVehicleUsage entities
 *
 * @author S Lizzio <shaun.lizzio@valtech.co.uk>
 */

namespace Olcs\Db\Service;

/**
 * LicenceVehicle Service
 *  - Takes care of the CRUD actions LicenceVehicleUsage entities
 *
 * @author S Lizzio <shaun.lizzio@valtech.co.uk>
 */
class LicenceVehicle extends ServiceAbstract
{

    /**
     * Returns an indexed array of valid search terms for this service / entity.
     *
     * @return array
     */
    public function getValidSearchFields()
    {
        return array('licence');
    }

    /**
     * Returns a list of matching records.
     *
     * @return array
     */
    public function getVehicleList()
    {
        $this->log(sprintf('Service Executing: \'%1$s\' with \'%2$s\'', __METHOD__, print_r(func_get_args(), true)));

        $data = func_get_arg(0);

        $searchFields = $this->pickValidKeys($data, $this->getValidSearchFields());

        $qb = $this->getEntityManager()->createQueryBuilder();
        $entityName = $this->getEntityName();
        $parts = explode('\\', $entityName);

        $qb->select('a');
        $qb->from($entityName, 'a');
        $params = array();

        foreach ($searchFields as $key => $value) {

            $field = $key;
            
            if (is_numeric($value)) {

                $qb->where("a.{$field} = :{$key}");

            } else {

                $qb->where("a.{$field} LIKE :{$key}");
            }
            $params[$key] = $value;
        }

        if ($this->canSoftDelete()) {
            $qb->where('a.is_deleted = 0');
        }

        if (!empty($params)) {
            $qb->setParameters($params);
        }

        $query = $qb->getQuery();

        $results = $query->getResult();

        $processedResults = $this->extractResultsArray($results);

        return array(
            'Count' => count($processedResults),
            'Results' => $processedResults
        );
    }
    
    /**
     * Method to extact vehicle results 
     * @param type $results
     */
    protected function extractResultsArray($results)
    {
        $extractedResults = array();
         if (!empty($results)) {

            $rows = array();
            $hydrator = $this->getDoctrineHydrator();

            foreach ($results as $row) {
                $vehicle = $row->getVehicle();
                $rows[] = $hydrator->extract($vehicle);
            }

            $extractedResults = $rows;
        }
        return $extractedResults;
    }
}
