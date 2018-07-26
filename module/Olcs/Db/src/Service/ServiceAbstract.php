<?php

/**
 * Abstract service that handles the generic crud functions for an entity
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Olcs\Db\Service;

use Doctrine\Instantiator\Instantiator;
use Olcs\Db\Traits\LanguageAwareTrait;
use Olcs\Logging\Log\Logger;
use Zend\ServiceManager\AbstractPluginManager;
use Olcs\Db\Traits\EntityManagerAwareTrait;
use Olcs\Db\Exceptions\NoVersionException;
use Doctrine\DBAL\LockMode;
use Olcs\Db\Utility\Paginator;
use Doctrine\ORM\Query;

/**
 * Abstract service that handles the generic crud functions for an entity
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class ServiceAbstract extends AbstractPluginManager
{
    use EntityManagerAwareTrait,
        LanguageAwareTrait;

    protected $entityNamespace = '\Dvsa\Olcs\Api\Entity\\';

    /**
     * Holds the Entity Name
     *
     * @var string
     */
    protected $entityName;

    protected $services = array();

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

    protected $replacementReferences = [];

    public function setEntityNamespace($namespace)
    {
        $this->entityNamespace = $namespace;
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
     * Returns the value of the entityName property.
     *
     * @return string
     */
    public function getEntityName()
    {
        if (!isset($this->entityName)) {
            $class = get_called_class();

            $parts = explode('\\', $class);

            return $this->formatEntityName(array_pop($parts));
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
        Logger::info('Service execution', ['location' => __METHOD__, 'data' => func_get_args()]);

        $data = $this->processAddressEntity($data);

        $entity = $this->getNewEntity();

        if (isset($data['_OPTIONS_']['cascade'])) {
            $data = $this->processCascades($entity, $data);
        }

        $hydrator = $this->getDoctrineHydrator();
        $hydrator->hydrate($data, $entity);

        $this->dbPersist($entity);
        $this->dbFlush();

        return $entity->getId();
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
        Logger::info('Service execution', ['location' => __METHOD__, 'data' => func_get_args()]);

        $criteria = array('id' => is_numeric($id) ? (int)$id : $id);

        list($qb, $replacements) = $this->getBundleQuery($criteria, $data);

        $query = $qb->getQuery();

        $language = $this->getLanguage();

        $query->setHint(
            \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER,
            'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
        );
        $query->setHint(\Gedmo\Translatable\TranslatableListener::HINT_FALLBACK, 1);
        $query->setHint(\Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE, $language);

        $query->setHint(\Doctrine\ORM\Query::HINT_INCLUDE_META_COLUMNS, true);

        $response = $query->getArrayResult();

        if (!$response) {
            return null;
        }

        $this->processReplacements($response, $replacements);

        return $response[0];
    }

    /**
     * Returns a list of matching records.
     *
     * @return array
     */
    public function getList($data)
    {
        Logger::info('Service execution', ['location' => __METHOD__, 'data' => func_get_args()]);

        $criteria = $this->pickValidKeys($data, $this->getValidSearchFields());

        list($qb, $replacements) = $this->getBundleQuery($criteria, $data);

        // Paginate
        $paginateQuery = $this->getServiceLocator()->get('PaginateQuery');
        $paginateQuery->setQueryBuilder($qb);
        $paginateQuery->setOptions(
            array_intersect_key($data, array_flip(['page', 'limit', 'sort', 'order']))
        );
        $paginateQuery->filterQuery();

        $query = $qb->getQuery()->setHydrationMode(Query::HYDRATE_ARRAY);

        $language = $this->getLanguage();

        $query->setHint(\Gedmo\Translatable\TranslatableListener::HINT_FALLBACK, 1);
        $query->setHint(\Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE, $language);
        $query->setHint(\Doctrine\ORM\Query::HINT_INCLUDE_META_COLUMNS, true);

        $paginator = new Paginator($query);

        $results = (array)$paginator->getIterator();
        $this->processReplacements($results, $replacements);

        return array(
            'Count' => $paginator->count(),
            'Results' => $results
        );
    }

    /**
     * Iterate through the data and find the ref data ids
     *
     * @param array $results
     * @param array $replacements
     * @return array
     */
    protected function getRefDataValues(&$results, $replacements)
    {
        $values = [];

        foreach ($results as &$result) {

            foreach ($replacements as $replacement) {

                $stack = $replacement['stack'];

                $values = array_merge($values, $this->getStackedValues($result, $stack));
            }
        }

        return $values;
    }

    /**
     * This method is recursive and builds a list of ref data ids for the given result node and stack
     * This method builds an array of references to be used when replacing the ids
     */
    protected function getStackedValues(&$result, $stack)
    {
        $this->camelCaseMetaFields($result);

        $values = [];
        $resultRef = &$result;

        // Iterate through the stack indexes to get deeper into the node
        while (count($stack) > 1) {
            $stackItem = array_shift($stack);

            if (!isset($resultRef[$stackItem])) {
                return $values;
            }

            $resultRef = &$resultRef[$stackItem];

            $this->camelCaseMetaFields($resultRef);

            // If we have a list here, we need to loop and recurse back through this method
            if ($this->isList($resultRef)) {
                foreach ($resultRef as &$value) {
                    $values = array_merge($values, $this->getStackedValues($value, $stack));
                }

                return $values;
            }
        }

        if (!is_array($resultRef) || empty($resultRef)) {
            return $values;
        }

        $stackItem = array_shift($stack);

        // Added extra check in case column is suffixed with Id
        if (!isset($resultRef[$stackItem]) && isset($resultRef[$stackItem . 'Id'])) {
            $value = $resultRef[$stackItem . 'Id'];

            $resultRef[$stackItem] = $value;

            $resultRef = &$resultRef[$stackItem];
            $this->replacementReferences[] = &$resultRef;
            $values[$value] = $value;
            return $values;
        }

        $resultRef = &$resultRef[$stackItem];

        if (!empty($resultRef)) {
            $this->replacementReferences[] = &$resultRef;
            $values[$resultRef] = $resultRef;
        } else {
            $resultRef = null;
        }

        return $values;
    }

    /**
     * This method replaces all referenced refdata ids with their ref data arrays
     */
    protected function replaceValues($refDataMap)
    {
        foreach ($this->replacementReferences as &$ref) {
            $ref = $refDataMap[$ref];
        }
    }

    /**
     * Does what it sez on't tin
     */
    protected function camelCaseMetaFields(&$array)
    {
        $filter = new \Zend\Filter\Word\UnderscoreToCamelCase();
        foreach ($array as $field => $value) {
            if (strstr($field, '_')) {
                $newField = lcfirst($filter->filter($field));

                if (array_key_exists($newField, $array)) {
                    // don't override existing fields if they've already been populated by a join!
                    unset($array[$field]);
                    continue;
                }

                $array[$newField] = $value;
                unset($array[$field]);
            }
        }
    }

    /**
     * Checks if an array is non assoc
     */
    protected function isList($array)
    {
        return array_keys($array) === range(0, count($array) - 1);
    }

    /**
     * If we have some Stacked ref data replacements, iterate through the results to find the ref data id's we need to
     * lookup. Once we have the ref data id's (and the translations) iterate through the results and replace the id's
     * with the ref data arrays
     *
     * @param array $results
     * @param array $replacements
     * @return array
     */
    protected function processReplacements(array &$results, array $replacements)
    {
        if (empty($replacements)) {
            return;
        }

        $refDatas = $this->getRefDataValues($results, $replacements);

        if (!empty($refDatas)) {
            $repo = $this->getEntityManager()->getRepository('\Dvsa\Olcs\Api\Entity\System\RefData');
            $qb = $repo->createQueryBuilder('r');

            $qb->where($qb->expr()->in('r.id', $refDatas));

            $query = $qb->getQuery();

            $language = $this->getLanguage();

            $query->setHint(
                \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER,
                'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
            );
            $query->setHint(\Gedmo\Translatable\TranslatableListener::HINT_FALLBACK, 1);
            $query->setHint(\Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE, $language);

            $refDataResults = $query->getArrayResult();

            $indexedRefDataResults = [];
            foreach ($refDataResults as $result) {
                $indexedRefDataResults[$result['id']] = $result;
            }
        } else {
            $indexedRefDataResults = [];
        }

        $this->replaceValues($indexedRefDataResults);

        return $results;
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
        Logger::info('Service execution', ['location' => __METHOD__, 'data' => func_get_args()]);

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

        list($qb, $replacements) = $this->getBundleQuery($criteria, $data);

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
        Logger::info('Service execution', ['location' => __METHOD__, 'data' => func_get_args()]);

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
        Logger::info('Service execution', ['location' => __METHOD__, 'data' => func_get_args()]);

        return $this->doUpdate($id, $data);
    }

    /**
     * Create a query builder object from the given bundle
     *
     * @param array $criteria
     * @param array $data
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function getBundleQuery($criteria, $data = array())
    {
        $params = array();

        $bundleConfig = $this->getBundleConfig($data);

        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select(array('m'))->from($this->getEntityName(), 'm');

        $replacements = [];
        if (!empty($bundleConfig)) {
            $bundleQuery = $this->getServiceLocator()->get('BundleQuery');
            $bundleQuery->setQueryBuilder($qb);
            $bundleQuery->build($bundleConfig);
            $replacements = $bundleQuery->getRefDataReplacements();
            $params = $bundleQuery->getParams();
        }

        $eb = $this->getServiceLocator()->get('ExpressionBuilder');

        $eb->setQueryBuilder($qb);
        $eb->setEntityManager($this->getEntityManager());
        $eb->setEntity($this->getEntityName());
        $eb->setParams($params);

        $expression = $eb->buildWhereExpression($criteria, 'm');

        if ($expression !== null) {
            $qb->andWhere($expression);
        }

        $qb->setParameters($eb->getParams());

        return [$qb, $replacements];
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

        if (isset($data['_OPTIONS_']['cascade'])) {
            $data = $this->processCascades($entity, $data);
        }

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
     * @return \DoctrineModule\Stdlib\Hydrator\DoctrineObject
     */
    protected function getDoctrineHydrator()
    {
        return $this->getServiceLocator()
            ->get('HydratorManager')
            ->get('DoctrineModule\Stdlib\Hydrator\DoctrineObject');
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
        $validKeys = [];

        foreach ($data as $key => $val) {
            if (is_numeric($key) || in_array($key, $keys)) {
                $validKeys[$key] = $val;
            }
        }

        return $validKeys;
    }

    /**
     * Returns a new instance of the entity.
     */
    protected function getNewEntity()
    {
        $entityName = $this->getEntityName();

        $instantiator = new Instantiator();
        return $instantiator->instantiate($entityName);
    }

    /**
     * Get ane entity by it's id
     *
     * @param int $id
     * @return object
     */
    protected function getEntityById($id)
    {
        return $this->getEntityByTypeAndId($this->getEntityName(), $id);
    }

    protected function getEntityByTypeAndId($entityType, $id)
    {
        $id = is_numeric($id) ? (int) $id : $id;

        return $this->getEntityManager()->find($entityType, $id);
    }

    /**
     * Get the service
     *
     * @param string $name
     * @return object
     */
    protected function getService($name)
    {
        if (!isset($this->services[$name])) {
            $serviceFactory = $this->getServiceLocator()->get('serviceFactory');
            $this->services[$name] = $serviceFactory->getService($name);
        }

        return $this->services[$name];
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
        if (isset($data['addresses']) && is_array($data['addresses'])) {

            $properties = $this->getEntityPropertyNames();

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

    protected function formatEntityName($entity)
    {
        $namespaces = $this->getServiceLocator()->get('Config')['entity_namespaces'];

        if (empty($namespaces[$entity])) {
            return $this->entityNamespace . $entity;
        }

        return $this->entityNamespace . $namespaces[$entity] . '\\' . $entity;
    }

    protected function processCascades($parentEntity, $data)
    {
        if (isset($data['_OPTIONS_']['cascade']['list'])) {
            $data = $this->processCascadeList($parentEntity, $data);
        }

        if (isset($data['_OPTIONS_']['cascade']['single'])) {
            $data = $this->processCascadeSingle($parentEntity, $data);
        }

        unset($data['_OPTIONS_']['cascade']);

        return $data;
    }

    protected function processCascadeList($parentEntity, $data)
    {
        foreach ($data['_OPTIONS_']['cascade']['list'] as $property => $cascadeOptions) {

            $entityClass = $this->formatEntityName($cascadeOptions['entity']);

            foreach ($data[$property] as $key => $entityData) {

                $data[$property][$key] = $this->generateCascadeEntity(
                    $entityClass,
                    $entityData,
                    $cascadeOptions,
                    $parentEntity
                );
            }
        }

        unset($data['_OPTIONS_']['cascade']['list']);

        return $data;
    }

    protected function processCascadeSingle($parentEntity, $data)
    {
        foreach ($data['_OPTIONS_']['cascade']['single'] as $property => $cascadeOptions) {

            $entityClass = $this->formatEntityName($cascadeOptions['entity']);

            $entityData = $data[$property];

            $data[$property] = $this->generateCascadeEntity($entityClass, $entityData, $cascadeOptions, $parentEntity);
        }

        unset ($data['_OPTIONS_']['cascade']['single']);

        return $data;
    }

    protected function generateCascadeEntity($entityClass, $entityData, $cascadeOptions, $parentEntity)
    {
        if (isset($entityData['id'])) {
            $cascadeEntity = $this->getEntityByTypeAndId($entityClass, $entityData['id']);
        } else {
            $cascadeEntity = new $entityClass();
        }

        if (isset($entityData['_OPTIONS_']['cascade'])) {
            $entityData = $this->processCascades($cascadeEntity, $entityData);
        }

        $hydrator = $this->getDoctrineHydrator();
        $hydrator->hydrate($entityData, $cascadeEntity);

        if (isset($cascadeOptions['parent'])) {
            $cascadeEntity->{'set' . ucfirst($cascadeOptions['parent'])}($parentEntity);
        }

        return $cascadeEntity;
    }
}
