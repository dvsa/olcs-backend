<?php

/**
 * Abstract service that handles the generic crud functions for an entity
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Olcs\Db\Service;

use Zend\ServiceManager\ServiceLocatorAwareTrait as ZendServiceLocatorAwareTrait;
use Olcs\Db\Traits\EntityManagerAwareTrait;
use Olcs\Db\Traits\LoggerAwareTrait as OlcsLoggerAwareTrait;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Olcs\Db\Exceptions\NoVersionException;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ORM\Query;

/**
 * Abstract service that handles the generic crud functions for an entity
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class ServiceAbstract
{
    use ZendServiceLocatorAwareTrait,
        EntityManagerAwareTrait,
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
     * Cache metadata
     *
     * @var array
     */
    protected $classMetadata = array();

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
     * Returns the value of the entityName property.
     *
     * @return string
     */
    public function getEntityName()
    {
        if (!isset($this->entityName)) {
            $entityPrefix = '\Olcs\Db\Entity\\';

            $class = get_called_class();

            $parts = explode('\\', $class);

            return $entityPrefix . array_pop($parts);
        }

        return $this->entityName;
    }

    /**
     * Should enter a value into the database and return the identifier for the record that has been created.
     *
     * @param array $data
     * @return mixed
     */
    public function create($data)
    {
        $this->getLogger()->info('Service excution', ['location' => __METHOD__, 'data' => func_get_args()]);

        $data = $this->processAddressEntity($data);

        $entity = $this->getNewEntity();

        $hydrator = $this->getDoctrineHydrator();

        $hydrator->hydrate($data, $entity);

        $this->dbPersist($entity);
        $this->dbFlush();

        return $this->getId($entity);
    }

    /**
     * Gets a matching record by identifying value.
     *
     * @param string|int $id
     * @param array $data
     *
     * @return array
     */
    public function get($id, array $data = array())
    {
        $this->getLogger()->info('Service excution', ['location' => __METHOD__, 'data' => func_get_args()]);

        $criteria = array('id' => is_numeric($id) ? (int)$id : $id);

        $qb = $this->getBundleQuery($criteria, $data);

        $query = $qb->getQuery();

        $response = $query->getArrayResult();

        if (!$response) {
            return null;
        }

        return $response[0];
    }

    /**
     * Returns a list of matching records.
     *
     * @return array
     */
    public function getList($data)
    {
        $this->getLogger()->info('Service excution', ['location' => __METHOD__, 'data' => func_get_args()]);

        $criteria = $this->pickValidKeys($data, $this->getValidSearchFields());

        $qb = $this->getBundleQuery($criteria, $data);

        // Paginate
        $paginateQuery = new \Olcs\Db\Utility\PaginateQuery($qb);
        $paginateQuery->setOptions(
            array_intersect_key($data, array_flip(['page', 'limit', 'sort', 'order']))
        );
        $paginateQuery->filterQuery();

        $query = $qb->getQuery()->setHydrationMode(Query::HYDRATE_ARRAY);

        $paginator = new Paginator($query);

        return array(
            'Count' => $paginator->count(),
            'Results' => (array)$paginator->getIterator()
        );
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
        $this->getLogger()->info('Service excution', ['location' => __METHOD__, 'data' => func_get_args()]);

        $entity = $this->getEntityById($id);

        if (!$entity) {
            return false;
        }

        $this->getEntityManager()->remove($entity);
        $this->dbFlush();

        return true;
    }

    /**
     * Delete a list of entities
     *
     * @param array $data
     */
    public function deleteList($data)
    {
        $criteria = $this->pickValidKeys($data, $this->getValidSearchFields());

        $qb = $this->getBundleQuery($criteria, $data);

        $query = $qb->getQuery();

        $results = $query->getResult();

        foreach ($results as $row) {
            $this->getEntityManager()->remove($row);
        }

        if (count($results) > 0) {
            $this->dbFlush();
        }

        return true;
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
        $this->getLogger()->info('Service excution', ['location' => __METHOD__, 'data' => func_get_args()]);

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
        $this->getLogger()->info('Service excution', ['location' => __METHOD__, 'data' => func_get_args()]);

        return $this->doUpdate($id, $data);
    }

    /**
     * Create a query builder object from the given bundle
     *
     * @param array $criteria
     * @param array $data
     * @param string $entityName
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function getBundleQuery($criteria, $data = array(), $entityName = null)
    {
        $params = array();

        $bundleConfig = $this->getBundleConfig($data);

        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select(array('m'))->from($this->getEntityName(), 'm');

        if (!empty($bundleConfig)) {
            // @todo Create factory for this
            $bundleQuery = new \Olcs\Db\Utility\BundleQuery($qb);
            $bundleQuery->setServiceLocator($this->getServiceLocator());

            $bundleQuery->build($bundleConfig);
            $params = $bundleQuery->getParams();
        }

        $eb = $this->getServiceLocator()->get('ExpressionBuilder');

        $eb->setQueryBuilder($qb);
        $eb->setEntityManager($this->getEntityManager());
        $eb->setEntity($entityName);
        $eb->setParams($params);

        $expression = $eb->buildWhereExpression($criteria, 'm');

        if ($expression !== null) {
            $qb->where($expression);
        }

        return $qb->setParameters($eb->getParams());
    }

    /**
     * Get entity id(s)
     *
     * @NOTE: Haven't unit tested this method yet, as this is awaiting changes to the doctrine ORM, the interface may be
     * difference
     *
     * @param EntityInterface $entity
     * @return mixed
     */
    protected function getId($entity)
    {
        $id = $this->getEntityManager()->getUnitOfWork()->getEntityIdentifier($entity);

        $class = $this->getEntityManager()->getClassMetadata(get_class($entity));

        $identifierConverter = new \Doctrine\ORM\Utility\IdentifierFlattener(
            $this->getEntityManager()->getUnitOfWork(),
            $this->getEntityManager()->getMetadataFactory()
        );

        $flatIds = $identifierConverter->flattenIdentifier($class, $id);

        if (count($flatIds) == 1) {
            return array_values($flatIds)[0];
        }

        return $flatIds;
    }

    protected function getBundleConfig($data)
    {
        $bundleConfig = null;

        if (isset($data['bundle'])) {
            $bundleConfig = json_decode($data['bundle'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid bundle configuration: Expected JSON');
            }
        }

        return $bundleConfig;
    }

    /**
     * Separated the update logic, as this is used by patch
     *
     * @param int $id
     * @param array $data
     * @return boolean
     * @throws NoVersionException
     */
    protected function doUpdate($id, $data)
    {
        // @NOTE if force is true, we should be able to force the update without a version number
        //  This should only be used in exception circumstances
        $force = (isset($data['_OPTIONS_']['force']) && $data['_OPTIONS_']['force']);

        if (!$force && !isset($data['version'])) {
            throw new NoVersionException('A version number must be specified to update an entity');
        }

        $data = $this->processAddressEntity($data);

        if ($force) {
            $entity = $this->getEntityManager()->find($this->getEntityName(), (int) $id);
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

        if (!$force) {
            $this->getEntityManager()->lock($entity, LockMode::OPTIMISTIC, $data['version']);
        }

        $this->dbPersist($entity);
        $this->dbFlush();

        return true;
    }

    /**
     * Return a new instance of DoctrineHydrator
     *
     * @return DoctrineObject
     */
    protected function getDoctrineHydrator()
    {
        return new DoctrineHydrator($this->getEntityManager());
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
     * @return \Olcs\Db\Entity\EntityInterface
     */
    protected function getNewEntity()
    {
        $entityName = $this->getEntityName();

        return new $entityName();
    }

    /**
     * Get ane entity by it's id
     *
     * @param int $id
     * @return object
     */
    protected function getEntityById($id)
    {
        $id = is_numeric($id) ? (int) $id : $id;

        return $this->getEntityManager()->find($this->getEntityName(), $id);
    }

    /**
     * Get the service
     *
     * @param string $name
     * @return object
     */
    protected function getService($name)
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
    protected function getValidSearchFields()
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
    protected function getReflectedEntity()
    {
        return new \ReflectionClass($this->getEntityName());
    }

    /**
     * Get an entity's property names as an array
     *
     * @return array
     */
    protected function getEntityPropertyNames()
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
    protected function processAddressEntity($data)
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
