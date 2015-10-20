<?php

/**
 * Query Handler Manager
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain;

use Olcs\Logging\Log\Logger;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\ConfigInterface;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Domain\QueryHandler\QueryHandlerInterface;
use Zend\ServiceManager\Exception\RuntimeException;

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
        $queryHandler = $this->get(get_class($query));

        Logger::debug(
            'Query Received: ' . get_class($query),
            ['data' => ['queryData' => $query->getArrayCopy()]]
        );

        $response = $queryHandler->handleQuery($query);

        Logger::debug(
            'Query Handler Response: ' . get_class($queryHandler),
            ['data' => ['response' => (array)$response]]
        );

        return $response;
    }

    public function validatePlugin($plugin)
    {
        if (!($plugin instanceof QueryHandlerInterface)) {
            throw new RuntimeException('Query handler does not implement QueryHandlerInterface');
        }
    }
}
