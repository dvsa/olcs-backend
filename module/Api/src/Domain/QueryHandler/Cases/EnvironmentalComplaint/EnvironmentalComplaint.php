<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Cases\EnvironmentalComplaint;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * EnvironmentalComplaint
 */
final class EnvironmentalComplaint extends AbstractQueryHandler
{
    protected $repoServiceName = 'Complaint';

    public function handleQuery(QueryInterface $query)
    {
        $complaint = $this->getRepo()->fetchUsingCaseId($query);

        return $complaint;
    }
}
