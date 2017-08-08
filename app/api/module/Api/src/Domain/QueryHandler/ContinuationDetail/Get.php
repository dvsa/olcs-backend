<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\ContinuationDetail;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Service\FinancialStandingHelperService;
use Dvsa\Olcs\Snapshot\Service\Snapshots\ApplicationReview\Section\ApplicationUndertakingsReviewService;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Entity\Licence\ContinuationDetail as ContinuationDetailEntity;
use Dvsa\Olcs\Api\Entity\Fee\Fee as FeeEntity;
use Dvsa\Olcs\Api\Domain\Repository\Fee as FeeRepo;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Entity\Cases\ConditionUndertaking;

/**
 * Get Continuation Detail
 */
class Get extends AbstractQueryHandler
{
    protected $repoServiceName = 'ContinuationDetail';
    protected $extraRepos = ['SystemParameter', 'Fee', 'Document', 'ConditionUndertaking'];

    /**
     * @var FinancialStandingHelperService
     */
    private $financialStandingHelper;

    /**
     * @var ApplicationUndertakingsReviewService
     */
    private $reviewService;

    /**
     * Factory
     *
     * @param ServiceLocatorInterface $serviceLocator Service manager
     *
     * @return $this
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->financialStandingHelper = $serviceLocator->getServiceLocator()->get('FinancialStandingHelperService');
        $this->reviewService = $serviceLocator->getServiceLocator()->get('Review\ApplicationUndertakings');

        return parent::createService($serviceLocator);
    }

    /**
     * Handle query
     *
     * @param QueryInterface $query DTO
     *
     * @return \Dvsa\Olcs\Api\Domain\QueryHandler\Result
     */
    public function handleQuery(QueryInterface $query)
    {
        /** @var ContinuationDetailEntity $continuationDetail */
        $continuationDetail = $this->getRepo()->fetchUsingId($query);
        $licence = $continuationDetail->getLicence();
        $documents = $this->getRepo('Document')
            ->fetchListForContinuationDetail($continuationDetail->getId(), Query::HYDRATE_ARRAY);
        $continuationFees = $this->getRepo('Fee')->fetchOutstandingContinuationFeesByLicenceId($licence->getId());
        $signatureDetails = [];
        if ($continuationDetail->getDigitalSignature()) {
            $signatureDetails = [
                'name' => $continuationDetail->getDigitalSignature()->getSignatureName(),
                'date' => $continuationDetail->getDigitalSignature()->getCreatedOn(),
                'dob' => $continuationDetail->getDigitalSignature()->getDateOfBirth(),
            ];
        }

        $financeRequired = $this->financialStandingHelper->getFinanceCalculationForOrganisation(
            $licence->getOrganisation()->getId()
        );

        $conditionsUndertakings = $this->groupConditionsUndertakings(
              $this->getRepo('ConditionUndertaking')->fetchListForLicenceReadOnly($licence->getId())
        );

        return $this->result(
            $continuationDetail,
            [
                'licence' => [
                    'organisation',
                    'trafficArea',
                    'licenceType',
                    'goodsOrPsv',
                ]
            ],
            [
                'financeRequired' => $financeRequired,
                'disableCardPayments' => $this->getRepo('SystemParameter')->getDisableSelfServeCardPayments(),
                'fees' => $this->resultList(
                    $continuationFees,
                    [
                        'feeType' => [
                            'feeType'
                        ],
                        'licence'
                    ]
                ),
                'documents' => $documents,
                'organisationTypeId' => $licence->getOrganisation()->getType()->getId(),
                'declarations' => $this->reviewService->getMarkupForLicence($licence),
                'disableSignatures' => $this->getRepo('SystemParameter')->getDisableGdsVerifySignatures(),
                'hasOutstandingContinuationFee' => count($continuationFees) > 0,
                'signature' => $signatureDetails,
                'reference' => $this->getPaymentReference($licence->getId()),
                'isFinancialEvidenceRequired' =>
                    $financeRequired > (
                        $continuationDetail->getAverageBalanceAmount()
                        + $continuationDetail->getFactoringAmount()
                        + $continuationDetail->getOverdraftAmount()
                        + $continuationDetail->getOtherFinancesAmount()
                    ),
                'conditionsUndertakings' => $conditionsUndertakings,
            ]
        );
    }

    /**
     * Return reference number of latest payment
     *
     * @param int $licenceId Licence id
     *
     * @return null|string
     */
    private function getPaymentReference($licenceId)
    {
        /** @var FeeRepo $repo */
        $repo = $this->getRepo('Fee');

        /** @var FeeEntity $latestFee */
        $latestFee = $repo->fetchLatestPaidContinuationFee($licenceId);
        if ($latestFee) {
            $today = (new DateTime('now'))->setTime(0, 0, 0);
            $feeDate = $latestFee->getLatestTransaction()->getCompletedDate(true)->setTime(0, 0, 0);
            $plus30Days = $feeDate->add(new \DateInterval('P30D'));
            if ($plus30Days < $today) {
                return null;
            }
            return $latestFee->getLatestPaymentRef();
        }

        return null;
    }

    /**
     * Group conditions and undertakings
     *
     * @param array $conditionsUndertakings conditions and undertakings
     *
     * @return array
     */
    private function groupConditionsUndertakings($conditionsUndertakings)
    {
        $licenceConditionsUndertakings = [];
        $ocConditionsUndertakings = [];
        $map = [
            ConditionUndertaking::TYPE_CONDITION => 'conditions',
            ConditionUndertaking::TYPE_UNDERTAKING => 'undertakings',
        ];

        /** @var ConditionUndertaking $cu */
        foreach ($conditionsUndertakings as $cu) {
            $condtitionType = $map[$cu->getConditionType()->getId()];
            if ($cu->getAttachedTo()->getId() === ConditionUndertaking::ATTACHED_TO_LICENCE) {
                $licenceConditionsUndertakings[$condtitionType][] = $cu;
            } else {
                $ocConditionsUndertakings[$cu->getOperatingCentre()->getId()][$condtitionType][] = $cu;
            }
        }
        // @todo sort by date
        return [
            'licence' => $licenceConditionsUndertakings,
            'operatingCentres' => $ocConditionsUndertakings,
        ];
    }
}
