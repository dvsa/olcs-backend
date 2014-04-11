<?php

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

abstract class ServiceAbstract implements OlcsRestServerInterface
{

    use ZendServiceLocatorAwareTrait,
        OlcsEntityManagerAwareTrait,
        OlcsLoggerAwareTrait;

    protected $entityName;
    protected $listControlKeys = array(
        'sortColumn',
        'sortReversed',
        'offset',
        'limit'
    );

    protected $validSearchFields = array(
    );

    /**
     * Should enter a value into the database and return the
     * identifier for the record that has been created.
     *
     * @param array $data
     * @return mixed
     */
    public function create($data)
    {
        $this->log(sprintf('Service Executing: \'%1$s\' with \'%2$s\'', __METHOD__, print_r(func_get_args(), true)));

        $entity = $this->getNewEntity();

        $hydrator = $this->getDoctrineHydrator();

        $hydrator->hydrate($data, $entity);

        $this->dbPersist($entity);
        $this->dbFlush();

        $id = $entity->getId();

        return $id;
    }

    /**
     * Gets a matching record by identifying value.
     *
     * @param string|int $id
     *
     * @return array
     */
    public function get($id)
    {
        $this->log(sprintf('Service Executing: \'%1$s\' with \'%2$s\'', __METHOD__, print_r(func_get_args(), true)));

        $entity = $this->getEntityById($id);

        if (!$entity) {
            return null;
        }

        $data = $this->extract($entity);

        return $data;
    }

    /**
     * Returns a list of matching records.
     *
     * @return array
     */
    public function getList()
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

            $field = $this->formatFieldName($key);

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

        if (!empty($results)) {

            $rows = array();

            foreach ($results as $row) {

                $hydrator = $this->getDoctrineHydrator();

                $rows[] = $hydrator->extract($row);
            }

            $results = $rows;
        }

        return array(
            'Count' => count($results),
            'Results' => $results
        );
    }

    /**
     * Update an entity
     *
     * @param mixed $id
     * @param array $data
     *
     * @return boolean success or failure
     */
    public function update($id, $data)
    {
        $this->log(sprintf('Service Executing: \'%1$s\' with \'%2$s\'', __METHOD__, print_r(func_get_args(), true)));

        return $this->doUpdate($id, $data);
    }

    /**
     * Updates the partial record based on identifying value.
     *
     * @param mixed $id
     * @param array $data
     *
     * @return boolean success or failure
     */
    public function patch($id, $data)
    {
        $this->log(sprintf('Service Executing: \'%1$s\' with \'%2$s\'', __METHOD__, print_r(func_get_args(), true)));

        return $this->doUpdate($id, $data);
    }

    /**
     * Separated the update logic, as this is used by patch
     *
     * @param int $id
     * @param array $data
     * @return boolean
     * @throws NoVersionException
     */
    private function doUpdate($id, $data)
    {
        if (!isset($data['version'])) {
            throw new NoVersionException('A version number must be specified to update an entity');
        }

        if ($this->canSoftDelete()) {
            $entity = $this->getUnDeletedById($id);
        } else {
            $entity = $this->getEntityManager()->find($this->getEntityName(), (int) $id, LockMode::OPTIMISTIC, $data['version']);
        }

        if (empty($entity)) {
            return false;
        }

        $entity->clearProperties(array_keys($data));

        $hydrator = $this->getDoctrineHydrator();
        $entity = $hydrator->hydrate($data, $entity);

        $this->getEntityManager()->lock($entity, LockMode::OPTIMISTIC, $data['version']);

        $this->dbPersist($entity);
        $this->dbFlush();

        return true;
    }

    /**
     * Deletes record based on identifying value.
     *
     * @param mixed $id
     *
     * @return boolean success or failure
     */
    public function delete($id)
    {
        $this->log(sprintf('Service Executing: \'%1$s\' with \'%2$s\'', __METHOD__, print_r(func_get_args(), true)));

        $entity = $this->getEntityById($id);

        if (!$entity) {
            return false;
        }

        if ($this->canSoftDelete()) {
            $entity->setIsDeleted(true);
            $this->dbPersist($entity);
        } else {
            $this->getEntityManager()->remove($entity);
        }
        $this->dbFlush();

        return true;
    }

    /**
     * Return a new instance of DoctrineHydrator
     *
     * @return DoctrineObject
     */
    public function getDoctrineHydrator()
    {
        return new DoctrineHydrator($this->getEntityManager());
    }

    /**
     * Return an instance of BundleHydrator (Possibly needs moving to a Zend service)
     *
     * @return BundleHydrator
     */
    public function getBundledHydrator()
    {
        $hydrator = $this->getDoctrineHydrator();

        return new BundleHydrator($hydrator);
    }

    /**
     * Picks out expected keys and returns just those.
     *
     * @param array $data
     * @param array $keys
     *
     * @return array
     */
    protected function pickValidKeys(array $data, array $keys)
    {
        return array_intersect_key($data, array_flip($keys));
    }

    /**
     * Returns a new instance of the entity.
     *
     * @return \OlcsEntities\Entity\AbstractEntity
     */
    public function getNewEntity()
    {
        $entityName = $this->getEntityName();
        return new $entityName();
    }

    /**
     * Returns the value of the entityName property.
     *
     * @return string
     */
    public function getEntityName()
    {
        if (!isset($this->entityName)) {
            $entityPrefix = '\OlcsEntities\Entity\\';

            $class = get_called_class();

            $parts = explode('\\', $class);

            return $entityPrefix . array_pop($parts);
        }

        return $this->entityName;
    }

    /**
     * Set the entity name
     *
     * @param string $entityName
     */
    public function setEntityName($entityName)
    {
        $this->entityName = $entityName;

        return $this;
    }

    /**
     * Whether you can soft delete the entity
     *
     * @return boolean
     */
    public function canSoftDelete()
    {
        return property_exists($this->getEntityName(), 'isDeleted');
    }

    /**
     * Get an entity if it's not soft deleted
     *
     * @param int $id
     *
     * @return object
     */
    public function getUnDeletedById($id)
    {
        return $this->getEntityManager()
                ->getRepository($this->getEntityName())
                ->findOneBy(array('id' => (int) $id, 'isDeleted' => 0));
    }

    /**
     * Get ane entity by it's id
     *
     * @param int $id
     * @return object
     */
    public function getEntityById($id)
    {
        if ($this->canSoftDelete()) {
            return $this->getUnDeletedById($id);
        }

        return $this->getEntityManager()->find($this->getEntityName(), (int) $id);
    }

    /**
     * Converts camel caps to underscore seperated
     *
     * @param sting $name
     *
     * @return string
     */
    protected function formatFieldName($name)
    {
        return preg_replace_callback(
            '/[A-Z]/', function($matches) {
            return '_' . strtolower($matches[0]);
        }, lcfirst($name)
        );
    }

    /**
     * Get the service
     *
     * @param string $name
     * @return object
     */
    public function getService($name)
    {
        $serviceFactory = $this->getServiceLocator()->get('serviceFactory');

        return $serviceFactory->getService($name);
    }

    /**
     * Method to extract data
     *
     * @param object $entity
     * @return array
     */
    protected function extract($entity)
    {
        $hydrator = $this->getDoctrineHydrator();

        $data = $hydrator->extract($entity);

        $data = $this->extractIds($data);

        return $data;
    }

    /**
     * Replace entities with their id's
     *
     * @param array $data
     * @return array
     */
    protected function extractIds($data)
    {
        foreach ($data as $key => $value) {

            if ($value instanceof \OlcsEntities\Entity\EntityInterface) {

                $data[$key] = $value->getId();
            } elseif ($value instanceof Collection) {

                $data[$key] = $this->extractIds($value->toArray());
            } elseif (is_array($value)) {

                $data[$key] = $this->extractIds($value);
            }
        }

        return $data;
    }

    /**
     * Returns an indexed array of valid search terms for this service / entity.
     *
     * @return array
     */
    public function getValidSearchFields()
    {
        if (empty($this->validSearchFields)) {

            $reflectedEntity = new \ReflectionClass($this->getEntityName());

            $this->validSearchFields = $reflectedEntity->getProperties();
        }

        return $this->validSearchFields;
    }

}
