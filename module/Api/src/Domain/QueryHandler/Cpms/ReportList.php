<?php

/**
 * Cpms Report List
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Cpms;

use Dvsa\Olcs\Api\Domain\CpmsAwareInterface;
use Dvsa\Olcs\Api\Domain\CpmsAwareTrait;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Cpms Report List
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class ReportList extends AbstractQueryHandler implements CpmsAwareInterface
{
    use CpmsAwareTrait;

    public function handleQuery(QueryInterface $query)
    {
        $data = $this->getCpmsService()->getReportList();

        return [
            'result' => $data,
            'count' => 0,
        ];
    }
}
