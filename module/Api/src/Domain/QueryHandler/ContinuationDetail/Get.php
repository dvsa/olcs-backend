<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\ContinuationDetail;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Service\FinancialStandingHelperService;
use Dvsa\Olcs\Snapshot\Service\Snapshots\ApplicationReview\Section\ApplicationUndertakingsReviewService;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Entity\Licence\ContinuationDetail as ContinuationDetailEntity;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Get Continuation Detail
 */
class Get extends AbstractQueryHandler
{
    protected $repoServiceName = 'ContinuationDetail';
    protected $extraRepos = ['SystemParameter', 'Fee', 'Document'];

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

        return $this->result(
            $continuationDetail,
            [
                'licence' => [
                    'organisation',
                    'trafficArea'
                ]
            ],
            [
                'financeRequired' => $this->financialStandingHelper->getFinanceCalculationForOrganisation(
                    $licence->getOrganisation()->getId()
                ),
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
            ]
        );
    }
}
