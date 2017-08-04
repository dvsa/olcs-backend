<?php

namespace Dvsa\Olcs\Snapshot\Service\Snapshots\ContinuationReview\Section;

use Dvsa\Olcs\Api\Domain\Repository\Document;
use Dvsa\Olcs\Api\Entity\Licence\ContinuationDetail;
use Dvsa\Olcs\Api\Service\FinancialStandingHelperService;

/**
 * Finance Continuation Review Service
 */
class FinanceReviewService extends AbstractReviewService
{
    /**
     * Format the readonly config from the given data
     *
     * @param ContinuationDetail $continuationDetail continuation detail
     *
     * @return array
     */
    public function getConfigFromData(ContinuationDetail $continuationDetail)
    {
        $financesRequiredAmount = $this->getFinancesRequiredAmount($continuationDetail);
        $items = [
            [
                'label' => 'continuations.finance.financial-amount-required',
                'value' => $this->formatMoney($financesRequiredAmount),
                'noEscape' => true,
            ],
            [
                'label' => 'continuations.finance.average-balance-amount',
                'value' => $this->formatMoney($continuationDetail->getAverageBalanceAmount()),
                'noEscape' => true,
            ],
            [
                'label' => 'continuations.finance.overdraft-facility',
                'value' => $this->formatMoney($continuationDetail->getOverdraftAmount()),
                'noEscape' => true
            ],
            [
                'label' => 'continuations.finance.factoring-amount',
                'value' => $this->formatMoney($continuationDetail->getFactoringAmount()),
                'noEscape' => true
            ],
        ];

        $totalAvailable = (float)$continuationDetail->getAverageBalanceAmount()
            + (float) $continuationDetail->getOverdraftAmount()
            + (float) $continuationDetail->getFactoringAmount();
        if ($financesRequiredAmount > $totalAvailable) {
            $items[] = [
                'label' => 'continuations.finance.other-available-finances',
                'value' => $this->formatMoney($continuationDetail->getOtherFinancesAmount()),
                'noEscape' => true
            ];

            if ((int)$continuationDetail->getOtherFinancesAmount() !== 0) {
                $items[] = [
                    'label' => 'continuations.finance.where-do-these-finances-come-from',
                    'value' => $continuationDetail->getOtherFinancesDetails()
                ];
            }
        }

        $financesSufficient = $financesRequiredAmount <=
            ($totalAvailable + (float)$continuationDetail->getOtherFinancesAmount());
        $items[] = [
            'label' => 'continuations.finance.finances-are-sufficient',
            'value' => $this->translate(($financesSufficient) ? 'Yes' : 'No'),
        ];
        if (!$financesSufficient) {
            if ($continuationDetail->getFinancialEvidenceUploaded() === true) {
                $value = implode("<br>", $this->getUploadedFiles($continuationDetail));
            } elseif ($continuationDetail->getFinancialEvidenceUploaded() === false) {
                $value = $this->translate('continuations.finance.send-in-post');
            } else {
                $value = $this->translate('None');
            }

            $items[] = [
                'label' => 'continuations.finance.financial-evidence',
                'value' => $value,
                'noEscape' => true,
            ];

        }
        return [
            'mainItems' => [
                [
                    'items' => $items
                ],
            ]
        ];
    }

    /**
     * Format an amount as money
     *
     * @param float $amount Amount
     *
     * @return string
     */
    private function formatMoney($amount)
    {
        if ((int)$amount === 0) {
            return $this->translate('None');
        }
        return '&pound;'. number_format($amount, 2);
    }

    /**
     * Get amount of finances required
     *
     * @param ContinuationDetail $continuationDetail Continutaion detail
     *
     * @return float
     */
    private function getFinancesRequiredAmount(ContinuationDetail $continuationDetail)
    {
        /** @var FinancialStandingHelperService $service */
        $service = $this->getServiceLocator()->get('FinancialStandingHelperService');

        return (float)$service->getFinanceCalculationForOrganisation(
            $continuationDetail->getLicence()->getOrganisation()->getId()
        );
    }

    /**
     * Get a list of filenames that have been uploaded
     *
     * @param ContinuationDetail $continuationDetail Continutaion detail
     *
     * @return array
     */
    private function getUploadedFiles(ContinuationDetail $continuationDetail)
    {
        /** @var Document $repo */
        $repo = $this->getServiceLocator()->get('RepositoryServiceManager')->get('Document');

        $documents = $repo->fetchListForContinuationDetail($continuationDetail->getId());

        $files = [];
        /** @var \Dvsa\Olcs\Api\Entity\Doc\Document $document */
        foreach ($documents as $document) {
            $files[] = $document->getDescription();
        }

        return $files;
    }
}
