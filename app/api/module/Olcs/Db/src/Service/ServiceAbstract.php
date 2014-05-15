<?php

/**
 * Abstract service that handles the generic crud functions for an entity
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Olcs\Db\Service;

use Zend\ServiceManager\ServiceLocatorAwareTrait as ZendServiceLocatorAwareTrait;
use Olcs\Db\Traits\EntityManagerAwareTrait as OlcsEntityManagerAwareTrait;
use Olcs\Db\Traits\LoggerAwareTrait as OlcsLoggerAwareTrait;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use OlcsEntities\Utility\BundleHydrator;
use Olcs\Db\Exceptions\NoVersionException;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * Abstract service that handles the generic crud functions for an entity
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class ServiceAbstract
{

    use ZendServiceLocatorAwareTrait,
        OlcsEntityManagerAwareTrait,
        OlcsLoggerAwareTrait;

    /**
     * Holds the Entity Name
     *
     * @var string
     */
    protected $entityName;

    /**
     * Holds the control keys
     *
     * @var array
     */
    protected $listControlKeys = array(
        'sortColumn',
        'sortReversed',
        'offset',
        'limit'
    );

    /**
     * Holds the valid search fields
     *
     * @var array
     */
    protected $validSearchFields = array();

    /**
     * Holds the entity properties
     *
     * @var array
     */
    protected $entityProperties = array();

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

        $data = $this->processAddressEntity($data);

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
    public function get($id, array $data = array())
    {
        $this->log(sprintf('Service Executing: \'%1$s\' with \'%2$s\'', __METHOD__, print_r(func_get_args(), true)));

        $entity = $this->getEntityById($id);

        if (!$entity) {
            return null;
        }

        $data = $this->getBundleCreator()->buildEntityBundle($entity, $data);

        return $data;
    }

    /**
     * Return an instance of BundleCreator
     *
     * @return \Olcs\Db\Service\Bundle\BundleCreator
     */
    public function getBundleCreator()
    {
        return new Bundle\BundleCreator($this->getDoctrineHydrator());
    }

    /**
     * Returns valid pagination values where they exist in the array given.
     *
     * @param array $data
     *
     * @return array
     */
    public function getPaginationValues(array $data)
    {
        return array_intersect_key($data, array_flip(['page', 'limit', 'sort', 'order']));
    }

    /**
     * Get the result offset
     *
     * @param int $page
     * @param int $limit
     * @return int
     */
    protected function getOffset($page, $limit)
    {
        return ($page * $limit) - $limit;
    }

    /**
     * Returns a list of matching records.
     *
     * @return array
     */
    public function getList($data)
    {
        $this->log(sprintf('Service Executing: \'%1$s\' with \'%2$s\'', __METHOD__, print_r(func_get_args(), true)));

        $searchFields = $this->pickValidKeys($data, $this->getValidSearchFields());

        $qb = $this->getEntityManager()->createQueryBuilder();
        $entityName = $this->getEntityName();

        $qb->select('a');
        $qb->from($entityName, 'a');

        $params = array();

        $whereMethod = 'where';

        foreach ($searchFields as $key => $value) {

            $field = $key;

            if (is_numeric($value)) {

                $qb->$whereMethod("a.{$field} = :{$key}");
                $whereMethod = 'andWhere';
            } else {

                $qb->$whereMethod("a.{$field} LIKE :{$key}");
                $whereMethod = 'andWhere';
            }
            $params[$key] = $value;
        }

        if ($this->canSoftDelete()) {
            $qb->$whereMethod('a.isDeleted = 0');
            $whereMethod = 'andWhere';
        }

        if (!empty($params)) {
            $qb->setParameters($params);
        }

        $pag = $this->getPaginationValues($data);
        $page = isset($pag['page']) ? $pag['page'] : 1;

        if (!isset($pag['limit']) || $pag['limit'] != 'all') {
            $limit = isset($pag['limit']) ? $pag['limit'] : 10;
            $qb->setFirstResult($this->getOffset($page, $limit));
            $qb->setMaxResults($limit);
        }

        $this->setOrderBy($qb, $data);

        $query = $qb->getQuery();

        $results = $query->getResult();

        if (!empty($results)) {

            $rows = array();

            foreach ($results as $row) {

                $rows[] = $this->getBundleCreator()->buildEntityBundle($row, $data);
            }

            $results = $rows;
        }

        $paginator = $this->getPaginator($query, false);

        return array(
            'Count' => count($paginator),
            'Results' => $results
        );
    }

    /**
     * Method to allow easier testing
     *
     * @param \Doctrine\ORM\Query $query
     * @param Bool $fetchJoinCollection
     * @return \Doctrine\ORM\Tools\Pagination\Paginator
     */
    public function getPaginator($query, $fetchJoinColumns = false)
    {
        return new Paginator($query, $fetchJoinColumns);
    }

    /**
     * Returns valid order by values where they exist in the array given.
     *
     * @param array $data
     *
     * @return array
     */
    public function getOrderByValues(array $data)
    {
        return array_intersect_key($data, array_flip(['sort', 'order']));
    }

    /**
     * Sets the sort by columns.
     *
     * @param unknown_type $qb
     * @param unknown_type $data
     */
    public function setOrderBy($qb, $data)
    {
        $orderByValues = $this->getOrderByValues($data);
        $sort = isset($orderByValues['sort']) ? $orderByValues['sort'] : '';
        if ($sort) {
            $sortString = 'a.' . $sort;
            $orderString = isset($orderByValues['order']) ? $orderByValues['order'] : 'ASC';

            $qb->orderBy($sortString, $orderString);
        }
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

        $data = $this->processAddressEntity($data);

        if ($this->canSoftDelete()) {
            $entity = $this->getUnDeletedById($id);
        } else {
            $entity = $this->getEntityManager()->find(
                $this->getEntityName(), (int) $id, LockMode::OPTIMISTIC, $data['version']
            );
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
                ->findOneBy(array('id' => (int) $id, 'isDeleted' => '0'));
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
     * Returns an array of valid search terms for the service / entity.
     * By default this simply wraps getEntityPropertyNames but the
     * methods are kept separate so they can be overridden individually
     * since search fields might not always equal all properties
     *
     * @return array
     */
    public function getValidSearchFields()
    {
        if (empty($this->validSearchFields)) {
            $this->validSearchFields = $this->getEntityPropertyNames();
        }

        return $this->validSearchFields;
    }

    /**
     * Get a reflected entity
     *
     * @return \ReflectionClass
     */
    public function getReflectedEntity()
    {
        return new \ReflectionClass($this->getEntityName());
    }

    /**
     * Get an entity's property names as an array
     *
     * @return array
     */
    public function getEntityPropertyNames()
    {
        if (empty($this->entityProperties)) {
            $this->entityProperties = array_map(
                function ($property) {
                    return $property->getName();
                },
                $this->getReflectedEntity()->getProperties()
            );
        }
        return $this->entityProperties;
    }


    /**
     * Find the address entities and process them
     *
     * @param array $data
     *
     * @return array
     */
    public function processAddressEntity($data)
    {
        $properties = $this->getEntityPropertyNames();

        if (isset($data['addresses']) && is_array($data['addresses'])) {

            foreach ($data['addresses'] as $key => $addressDetails) {
                if (!in_array($key, $properties)) {
                    continue;
                }

                $addressService = $this->getService('Address');

                // If we are updating an address
                if (isset($addressDetails['id']) && !empty($addressDetails['id'])) {
                    $addressService->update($addressDetails['id'], $addressDetails);

                    $data[$key] = $addressDetails['id'];
                } else {
                    $data[$key] = $addressService->create($addressDetails);
                }
            }

            unset($data['addresses']);
        }

        return $data;
    }
}
