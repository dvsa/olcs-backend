<?php

/**
 * Query Handler Manager
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain;

use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Transfer\Query\LoggerOmitResponseInterface;
use Olcs\Logging\Log\Logger;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\ConfigInterface;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Domain\QueryHandler\QueryHandlerInterface;
use Zend\ServiceManager\Exception\RuntimeException;
use Dvsa\Olcs\Api\Domain\ValidationHandler\ValidationHandlerInterface;

/**
 * Query Handler Manager
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class QueryHandlerManager extends AbstractPluginManager implements QueryHandlerInterface
{
    public function __construct(ConfigInterface $config = null)
    {
        $this->setShareByDefault(false);

        if ($config) {
            $config->configureServiceManager($this);
        }
    }

    public function handleQuery(QueryInterface $query)
    {
        $queryFqcl = get_class($query);

        $queryHandler = $this->get($queryFqcl);

        Logger::debug(
            'Query Received: ' . $queryFqcl,
            ['data' => ['queryData' => $query->getArrayCopy()]]
        );

        $queryHandlerFqcl = get_class($queryHandler);

        $this->validateDto($query, $queryHandlerFqcl);

        $response = $queryHandler->handleQuery($query);

        if ($query instanceof LoggerOmitResponseInterface) {
            $logData = ['*** OMITTED ***'];
        } else {
            $logData = (array)$response;
        }

        Logger::debug(
            'Query Handler Response: ' . $queryHandlerFqcl,
            ['data' => ['response' => $logData]]
        );

        return $response;
    }

    public function validatePlugin($plugin)
    {
        if (!($plugin instanceof QueryHandlerInterface)) {
            throw new RuntimeException('Query handler does not implement QueryHandlerInterface');
        }
    }

    protected function validateDto($dto, $queryHandlerFqcl)
    {
        $vhm = $this->getServiceLocator()->get('ValidationHandlerManager');

        /** @var ValidationHandlerInterface $validationHandler */
        $validationHandler = $vhm->get($queryHandlerFqcl);

        if (!$validationHandler->isValid($dto)) {
            throw new ForbiddenException('You do not have access to this resource');
        }
    }
}
