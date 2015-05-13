<?php

/**
 * Query Handler Manager
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain;

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
        if ($config) {
            $config->configureServiceManager($this);
        }
    }

    public function handleQuery(QueryInterface $query)
    {
        return $this->get(get_class($query))->handleQuery($query);
    }

    public function validatePlugin($plugin)
    {
        if (!($plugin instanceof QueryHandlerInterface)) {
            throw new RuntimeException('Query handler does not implement QueryHandlerInterface');
        }
    }
}
