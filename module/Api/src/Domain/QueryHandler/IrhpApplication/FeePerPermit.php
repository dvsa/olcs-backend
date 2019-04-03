<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\IrhpApplication;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\Fee\FeeType;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Query\IrhpApplication\FeePerPermit as FeePerPermitQuery;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Fee per permit
 */
class FeePerPermit extends AbstractQueryHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_ECMT];

    protected $repoServiceName = 'IrhpApplication';

    protected $extraRepos = ['FeeType'];

    /**
     * Handle query
     *
     * @param QueryInterface|FeePerPermitQuery $query query
     *
     * @return array
     */
    public function handleQuery(QueryInterface $query)
    {
        $irhpApplication = $this->getRepo()->fetchById($query->getId());
        if ($irhpApplication->getIrhpPermitType()->getId() != IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL) {
            return ['feePerPermit' => 'Not applicable'];
        }

        $feeTypeRepo = $this->getRepo('FeeType');

        $applicationFeeType = $feeTypeRepo->getLatestByProductReference(
            FeeType::FEE_TYPE_IRHP_APP_BILATERAL_PRODUCT_REF
        );

        $issueFeeType = $feeTypeRepo->getLatestByProductReference(
            FeeType::FEE_TYPE_IRHP_ISSUE_BILATERAL_PRODUCT_REF
        );

        $feePerPermit = $applicationFeeType->getFixedValue() + $issueFeeType->getFixedValue();
        return ['feePerPermit' => $feePerPermit];
    }
}
