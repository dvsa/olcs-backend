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

abstract class ServiceAbstract implements OlcsRestServerInterface
{
    use ZendServiceLocatorAwareTrait;
    use OlcsEntityManagerAwareTrait;
    use OlcsLoggerAwareTrait;

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

        $hydrator = new DoctrineHydrator($this->getEntityManager());

        $bundleHydrator = new BundleHydrator($hydrator);

        $entity = $bundleHydrator->getNestedEntityFromEntities($data);

        // Just get the first entity for now
        //  This is where we can work on the magic to save multiple entities at once
        $entity = current($entity);

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

        $hydrator = new DoctrineHydrator($this->getEntityManager());

        $bundleHydrator = new BundleHydrator($hydrator);

        return $bundleHydrator->getTopLevelEntitiesFromNestedEntity($entity);
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

        $listControlParams = $this->extractListControlParams($data);

        $searchFields = $this->pickValidKeys($data, $this->getValidSearchFields());

        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('a');
        $qb->from($this->getEntityName(), 'a');

        foreach ($searchFields as $key => $value) {

            if (is_numeric($value)) {

                $qb->where("a.{$key} = :{$key}");

            } else {

                $qb->where("a.{$key} LIKE :{$key}");
            }
            $qb->setParameter($key, $value);
        }

        if ($this->canSoftDelete()) {
            $qb->where('a.isDeleted = false');
        }

        $results = $qb->getQuery()->getResult();

        $responseData = array();

        if (!empty($results)) {

            $hydrator = new DoctrineHydrator($this->getEntityManager());

            $bundleHydrator = new BundleHydrator($hydrator);

            $responseData = $bundleHydrator->getTopLevelEntitiesFromNestedEntity($results);
        }

        return array(
            'Count' => count($results),
            'Results' => $responseData
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

        if (!isset($data['version'])) {
            throw new NoVersionException('A version number must be specified to update an entity');
        }

        if ($this->canSoftDelete()) {
            $entity = $this->getUnDeletedById($id);
        } else {
            $entity = $this->getEntityManager()->find($this->getEntityName(), (int)$id, LockMode::OPTIMISTIC, $data['version']);
        }

        if (!$entity) {
            return false;
        }

        $hydrator = new DoctrineHydrator($this->getEntityManager());
        $entity = $hydrator->hydrate($data, $entity);

        $this->getEntityManager()->lock($entity, LockMode::OPTIMISTIC, $data['version']);

        $this->dbPersist($entity);
        $this->dbFlush();

        return true;
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

        return $this->update($id, $data);
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

        $this->getEntityManager()->remove($entity);
        $this->dbFlush();

        return true;
    }

    /**
     * Extracts the list control perameters and returns just those.
     *
     * @param array $data
     *
     * @return array
     */
    public function extractListControlParams(array $data)
    {
        return $this->pickValidKeys($data, $this->getListControlKeys());
    }

    /**
     * Returns an indexed array containing keys for list control.
     *
     * @return array
     */
    protected function getListControlKeys()
    {
        return array(
            'sortColumn',
            'sortReversed',
            'offset',
            'limit'
        );
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
        return $this->entityName;
    }

    /**
     * Whether you can soft delete the entity
     *
     * @return boolean
     */
    private function canSoftDelete()
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
    private function getUnDeletedById($id)
    {
        return $this->getEntityManager()
            ->getRepository($this->getEntityName())
            ->findOneBy(array('id' => (int)$id, 'isDeleted' => false));
    }

    /**
     * Get ane entity by it's id
     *
     * @param int $id
     * @return object
     */
    private function getEntityById($id)
    {
        if ($this->canSoftDelete()) {
            return $this->getUnDeletedById($id);
        }

        return $this->getEntityManager()->find($this->getEntityName(), (int)$id);
    }

    /**
     * Returns an indexed array of valid search terms for this service / entity.
     *
     * @return array
     */
    abstract public function getValidSearchFields();
}
