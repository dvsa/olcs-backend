<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Permits;

use Dvsa\Olcs\Api\Domain\Query\Permits\EcmtApplicationIssueFeePerPermit as EcmtApplicationIssueFeePerPermitQuery;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * ECMT Application issue fee per permit
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class EcmtApplicationIssueFeePerPermit extends AbstractQueryHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_PERMITS];

    protected $repoServiceName = 'EcmtPermitApplication';

    protected $extraRepos = ['FeeType'];

    /**
     * Handle query
     *
     * @param QueryInterface|EcmtApplicationIssueFeePerPermitQuery $query query
     *
     * @return array
     */
    public function handleQuery(QueryInterface $query)
    {
        $application = $this->getRepo()->fetchUsingId($query);

        $feeType = $this->getRepo('FeeType')->getLatestByProductReference(
            $application->getIssueFeeProductReference()
        );

        return [
            'feePerPermit' => $feeType->getFixedValue()
        ];
    }
}
