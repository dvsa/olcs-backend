<?php
namespace Olcs\Db\Service;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait as ZendServiceLocatorAwareTrait;
use Olcs\Db\Traits\EntityManagerAwareTrait as OlcsEntityManagerAwareTrait;
use Olcs\Db\Utility\RestServerInterface as OlcsRestServerInterface;

abstract class ServiceAbstract implements OlcsRestServerInterface
{
    use ZendServiceLocatorAwareTrait;
    use OlcsEntityManagerAwareTrait;

    public function getEntity()
    {
        return new $this->entity;
    }

    /**
     * Takes the data and creates a record in the database.
     *
     * @param array $data
     */
    public function create($data)
    {
        $entity = $this->getEntity();

        $this->hydrateEntity($entity, $data);

        $this->dbPersist($entity);
        $this->dbFlush();

        return $entity->getId();
    }

    public function getList()
    {}

    public function get($id)
    {}

    public function update($id, $data)
    {}

    public function patch($id, $data)
    {}

    public function delete($id)
    {}

    public function log()
    {
        // will be in a trait
    }

    public function hydrateEntity(\Olcs\Db\Entity\AbstractEntity $entity, array $data)
    {
        foreach ($data as $propertyName => $propertyValue) {
            $this->setEntityProperty($entity, $propertyName, $propertyValue);
        }
    }

    public function setEntityProperty(\Olcs\Db\Entity\AbstractEntity $entity, $property, $value)
    {
        $method = 'set' . ucfirst($property);

        if (method_exists($entity, $method)) {
            is_scalar($value) ? $this->log(
                "Attempting to set '{$property}' with value '{$value}' using method '{$method}'",
                \Zend\Log\Logger::DEBUG
            ) : '';
            call_user_func(array($entity, $method), $value);
            is_scalar($value) ? $this->log(
                "SUCCESS: Attempting to set '{$property}' with value '{$value}' using method '{$method}'",
                \Zend\Log\Logger::DEBUG
            ) : '';
        } else {
            is_scalar($value) ? $this->log(
                "FAILED: Attempting to set '{$property}' with value '{$value}' using method '{$method}'",
                \Zend\Log\Logger::ERR
            ) : '';
            is_scalar($value) ? $this->log(
                "Method: '" . get_class($entity) . "::{$method}' does not exist",
                \Zend\Log\Logger::ERR
            ) : '';
        }

        return $entity;
    }
}
