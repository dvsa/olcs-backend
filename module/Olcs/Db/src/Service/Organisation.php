<?php

/**
 * Organisation Service
 *  - Takes care of the CRUD actions Organisation entities
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Olcs\Db\Service;

use Zend\ServiceManager\ServiceLocatorAwareTrait as ZendServiceLocatorAwareTrait;
use Olcs\Db\Traits\EntityManagerAwareTrait as OlcsEntityManagerAwareTrait;
use Olcs\Db\Traits\LoggerAwareTrait as OlcsLoggerAwareTrait;
use Olcs\Db\Utility\RestServerInterface as OlcsRestServerInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use OlcsEntities\Utility\BundleHydrator;
use Olcs\Db\Exceptions\NoVersionException;
use Doctrine\DBAL\LockMode;
use Doctrine\Common\Collections\Collection;

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
     * Gets an organisation record by licenceId.
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
    
    /**
     * Update an organisation record by licenceId.
     * 
     * @todo Possibly use join... needs performence review
     * 
     * @param int $id
     * @param array $data
     * @throws NoVersionException
     * @return NULL|boolean
     */
    public function updateByLicenceId($id, $data)
    {
        if (!isset($data['version'])) {
            throw new NoVersionException('A version number must be specified to update an entity');
        }
        
        $licenceEntity = $this->getEntityManager()->getRepository('OlcsEntities\Entity\Licence')->findOneBy(['id' => $id]);
        if (empty($licenceEntity)) {
            return null;
        }
        
        $orgEntity = $licenceEntity->getOrganisation();
        if (empty($orgEntity)) {
            return null;
        }
        
        //$orgEntity->clearProperties(array_keys($data));
        
        $hydrator = $this->getDoctrineHydrator();
        $orgEntity = $hydrator->hydrate($data, $orgEntity);
        
        $this->getEntityManager()->lock($orgEntity, LockMode::OPTIMISTIC, $data['version']);
        
        $this->dbPersist($orgEntity);
        $this->dbFlush();
        
        return true;
    }

}
