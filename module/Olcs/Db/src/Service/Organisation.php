<?php

/**
 * Organisation Service
 *  - Takes care of the CRUD actions Organisation entities
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Olcs\Db\Service;

/**
 * Organisation Service
 *  - Takes care of the CRUD actions Organisation entities
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Organisation extends ServiceAbstract
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
    
    /**
     * Gets a organisation record by licenceId.
     * @todo Possibly use join... needs performence review
     *
     * @param int $id
     *
     * @return array
     */
    public function getByLicenceId($id)
    {
        $this->log(sprintf('Service Executing: \'%1$s\' with \'%2$s\'', __METHOD__, print_r(func_get_args(), true)));
        
        $licenceEntity = $this->getEntityManager()->getRepository('OlcsEntities\Entity\Licence')->findOneBy(['id' => $id]);
        if (empty($licenceEntity)) {
            return null;
        }
        $orgEntity = $licenceEntity->getOrganisation();
        if (empty($orgEntity)) {
            return null;
        }
        
        $data = $this->extract($orgEntity);
        return $data;
    }

}
