<?php

/**
 * SystemParameter
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\SystemParameter;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * SystemParameter
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class SystemParameter extends AbstractQueryHandler
{
    protected $repoServiceName = 'SystemParameter';

    public function handleQuery(QueryInterface $query)
    {
        $systemParameter = $this->getRepo()->fetchUsingId($query);
        return $this->result($systemParameter);
    }
}
