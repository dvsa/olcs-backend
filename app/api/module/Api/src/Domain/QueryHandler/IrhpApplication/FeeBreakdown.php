<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\IrhpApplication;

use DateTime;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\Fee\FeeType;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Query\IrhpApplication\FeeBreakdown as FeeBreakdownQuery;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Fee breakdown
 */
class FeeBreakdown extends AbstractQueryHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_PERMITS];

    protected $repoServiceName = 'IrhpApplication';

    /** @var IrhpApplication */
    private $irhpApplication;

    /** @var array */
    private $issueFeeMappings = [];

    /** @var DateTime */
    private $issueFeeInvoicedDate;

    /** @var FeeType */
    private $applicationFeeType;

    /**
     * Handle query
     *
     * @param QueryInterface|FeeBreakdownQuery $query query
     *
     * @return array
     */
    public function handleQuery(QueryInterface $query)
    {
        $this->irhpApplication = $this->getRepo()->fetchUsingId($query);
        if (!$this->irhpApplication->getIrhpPermitType()->isMultilateral()) {
            // return an empty table so that the frontend knows not to display it
            return [];
        }

        $this->applicationFeeType = $this->irhpApplication->getLatestOutstandingApplicationFee()->getFeeType();

        $issueFees = $this->irhpApplication->getOutstandingIssueFees();
        $this->populateIssueFeeMappings($issueFees);
        $this->issueFeeInvoicedDate = $issueFees[0]->getInvoicedDate(true);

        $rows = [];
        foreach ($this->irhpApplication->getIrhpPermitApplications() as $irhpPermitApplication) {
            if ($irhpPermitApplication->getPermitsRequired() > 0) {
                $rows[] = $this->generateRow($irhpPermitApplication);
            }
        }

        return $rows;
    }

    /**
     * Populates a key/value lookup array of product references to fee types
     *
     * @param array $issueFees
     */
    private function populateIssueFeeMappings(array $issueFees)
    {
        foreach ($issueFees as $issueFee) {
            $issueFeeType = $issueFee->getFeeType();
            $this->issueFeeMappings[$issueFeeType->getProductReference()] = $issueFeeType;
        }
    }

    /**
     * Returns an array representing a table row within the fee breakdown
     *
     * @param IrhpPermitApplication $irhpPermitApplication
     *
     * @return array
     */
    private function generateRow(IrhpPermitApplication $irhpPermitApplication)
    {
        $issueFeeProductReference = $irhpPermitApplication->getIssueFeeProductReference($this->issueFeeInvoicedDate);

        $feePerPermit = $this->irhpApplication->getFeePerPermit(
            $this->applicationFeeType,
            $this->issueFeeMappings[$issueFeeProductReference]
        );

        $numberOfPermits = $irhpPermitApplication->getPermitsRequired();
        $irhpPermitStock = $irhpPermitApplication->getIrhpPermitWindow()->getIrhpPermitStock();
        $irhpPermitStockValidFrom = $irhpPermitStock->getValidFrom(true);
        $irhpPermitStockValidTo = $irhpPermitStock->getValidTo(true);

        $validFrom = $irhpPermitStockValidFrom;
        if ($this->issueFeeInvoicedDate > $irhpPermitStockValidFrom) {
            $validFrom = $this->issueFeeInvoicedDate;
        }

        return [
            'year' => $irhpPermitStockValidTo->format('Y'),
            'validFromTimestamp' => $validFrom->getTimestamp(),
            'validToTimestamp' => $irhpPermitStockValidTo->getTimestamp(),
            'feePerPermit' => $feePerPermit,
            'numberOfPermits' => $numberOfPermits,
            'totalFee' => $numberOfPermits * $feePerPermit,
        ];
    }
}
