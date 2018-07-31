<?php

/**
 * Query Handler Manager
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain;

use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Transfer\Query\LoggerOmitResponseInterface;
use Interop\Container\ContainerInterface;
use Olcs\Logging\Log\Logger;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\ConfigInterface;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Domain\QueryHandler\QueryHandlerInterface;
use Zend\ServiceManager\Exception\InvalidServiceException;
use Dvsa\Olcs\Api\Domain\ValidationHandler\ValidationHandlerInterface;

/**
 * Query Handler Manager
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class QueryHandlerManager extends AbstractPluginManager
{
    public function __construct(ConfigInterface $config = null)
    {
        $this->configure(['sharedByDefault' => false]);

        if ($config) {
            $config->configureServiceManager($this);
        }
    }

    public function handleQuery(QueryInterface $query, $validate = true)
    {
        $start = microtime(true);

        $queryFqcl = get_class($query);

        $queryHandler = $this->get($queryFqcl);

        Logger::debug(
            'Query Received: ' . $queryFqcl,
            ['data' => ['queryData' => $query->getArrayCopy()]]
        );

        $queryHandlerFqcl = get_class($queryHandler);

        if ($validate) {
            $this->validateDto($query, $queryHandlerFqcl);
        }

        $response = $queryHandler->handleQuery($query);
        if ($query instanceof LoggerOmitResponseInterface) {
            $logData = ['*** OMITTED ***'];
        } elseif (is_object($response) && $response instanceof QueryHandler\BundleSerializableInterface) {
            // if response is an Entity
            $logData = $response->serialize();
        } else {
            $logData = (array)$response;
        }

        Logger::debug(
            'Query Handler Response: ' . $queryHandlerFqcl,
            [
                'data' => [
                    'response' => $logData,
                    'time' => round(microtime(true) - $start, 5),
                ]
            ]
        );

        return $response;
    }

    public function validatePlugin($plugin)
    {
        if (!($plugin instanceof QueryHandlerInterface)) {
            throw new InvalidServiceException('Query handler does not implement QueryHandlerInterface');
        }
    }

    protected function validateDto($dto, $queryHandlerFqcl)
    {
        $vhm = $this->getServiceLocator()->get('ValidationHandlerManager');

        /** @var ValidationHandlerInterface $validationHandler */
        $validationHandler = $vhm->get($queryHandlerFqcl);

        if (!$validationHandler->isValid($dto)) {
            Logger::debug(
                'DTO Failed validation',
                [
                    'handler' => $queryHandlerFqcl,
                    'data' => $dto->getArrayCopy(),
                ]
            );
            throw new ForbiddenException('You do not have access to this resource');
        }
    }

    public function getServiceLocator()
    {
        return $this->creationContext;
    }

    public function setServiceLocator(ContainerInterface $sl)
    {
        $this->creationContext = $sl;
    }
}
