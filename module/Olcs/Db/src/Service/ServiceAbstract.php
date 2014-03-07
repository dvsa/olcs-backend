<?php
namespace Olcs\Db\Service;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait as ZendServiceLocatorAwareTrait;
use Olcs\Db\Traits\EntityManagerAwareTrait as OlcsEntityManagerAwareTrait;
use Olcs\Db\Traits\LoggerAwareTrait as OlcsLoggerAwareTrait;
use Olcs\Db\Utility\RestServerInterface as OlcsRestServerInterface;
use Zend\Stdlib\Hydrator\ClassMethods as ZendClassMethodsHydrator;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

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

        $entity = $this->getNewEntity();

        $hydrator = new ZendClassMethodsHydrator();
        $hydrator->hydrate($data, $entity);

        $this->dbPersist($entity);
        $this->dbFlush();

        $id = $entity->getId();

        return $id;
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
        //print_r($listControlParams);

        $searchFields = $this->pickValidKeys($data, $this->getValidSearchFields());
        //print_r($searchFields);

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

        $results = $qb->getQuery()->getResult();

        $hydrator = new DoctrineHydrator($this->getEntityManager());
        $data = array();
        foreach ($results as $entity) {
            $data[] = $hydrator->extract($entity);
        }

        return $data;
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

        $entity = $this->getEntityManager()->find($this->getEntityName(), (int)$id);
        if (!$entity) {
            return null;
        }
        $hydrator = new DoctrineHydrator($this->getEntityManager());
        return $hydrator->extract($entity);
    }

    /**
     * NOT WORKING!!!!
     *
     * @param mixed $id
     * @param array $data
     *
     * @return boolean success or failure
     */
    public function update($id, $data)
    {
        $this->log(sprintf('Service Executing: \'%1$s\' with \'%2$s\'', __METHOD__, print_r(func_get_args(), true)));

        $entity = $this->getEntityManager()->find($this->getEntityName(), (int)$id);

        if (!$entity) {
            return false;
        }

        $hydrator = new DoctrineHydrator($this->getEntityManager());
        $entity = $hydrator->hydrate($data, $entity);

        $this->dbPersist($entity);

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

        $entity = $this->getEntityManager()->find($this->getEntityName(), (int)$id);

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
     * @return \Olcs\Db\Entity\AbstractEntity
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
     * Returns an indexed array of valid search terms for this service / entity.
     *
     * @return array
     */
    abstract public function getValidSearchFields();
}
