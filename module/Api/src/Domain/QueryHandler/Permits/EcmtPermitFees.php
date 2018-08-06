<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Permits;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Doctrine\ORM\Query;

/**
 * Retrieve Ecmt Permit Fees
 *
 * @author Kollol Shamsuddin <kol.shamsudin@capgemini.com>
 * @author Jason De Jonge <jason.de-jonge@capgemini.com>
 */
class EcmtPermitFees extends AbstractQueryHandler
{
    protected $repoServiceName = 'FeeType';

    public function handleQuery(QueryInterface $query)
    {
        $repo = $this->getRepo();

        $result = [];
        foreach($query->getProductReferences() as $feeProductReference)
        {
            $result['fee'][$feeProductReference] = $repo->getLatestForEcmtPermit($feeProductReference);
        }

        return $result;
    }
}
