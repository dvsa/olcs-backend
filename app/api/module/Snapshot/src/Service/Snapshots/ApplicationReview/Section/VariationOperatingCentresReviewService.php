<?php

/**
 * Variation Operating Centres Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Snapshot\Service\Snapshots\ApplicationReview\Section;

/**
 * Variation Operating Centres Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationOperatingCentresReviewService extends AbstractOperatingCentresReviewService
{
    /** @var PsvOperatingCentreReviewService */
    private $psvOperatingCentreReviewService;

    /** @var VariationPsvOcTotalAuthReviewService */
    private $variationPsvOcTotalAuthReviewService;

    /** @var GoodsOperatingCentreReviewService */
    private $goodsOperatingCentreReviewService;

    /** @var VariationGoodsOcTotalAuthReviewService */
    private $variationGoodsOcTotalAuthReviewService;

    /**
     * Create service instance
     *
     * @param AbstractReviewServiceServices $abstractReviewServiceServices
     * @param PsvOperatingCentreReviewService $psvOperatingCentreReviewService
     * @param ApplicationPsvOcTotalAuthReviewService $applicationPsvOcTotalAuthReviewService
     * @param GoodsOperatingCentreReviewService $goodsOperatingCentreReviewService
     * @param ApplicationGoodsOcTotalAuthReviewService $applicationGoodsOcTotalAuthReviewService
     *
     * @return VariationOperatingCentresReviewService
     */
    public function __construct(
        AbstractReviewServiceServices $abstractReviewServiceServices,
        PsvOperatingCentreReviewService $psvOperatingCentreReviewService,
        VariationPsvOcTotalAuthReviewService $variationPsvOcTotalAuthReviewService,
        GoodsOperatingCentreReviewService $goodsOperatingCentreReviewService,
        VariationGoodsOcTotalAuthReviewService $variationGoodsOcTotalAuthReviewService
    ) {
        parent::__construct($abstractReviewServiceServices);
        $this->psvOperatingCentreReviewService = $psvOperatingCentreReviewService;
        $this->variationPsvOcTotalAuthReviewService = $variationPsvOcTotalAuthReviewService;
        $this->goodsOperatingCentreReviewService = $goodsOperatingCentreReviewService;
        $this->variationGoodsOcTotalAuthReviewService = $variationGoodsOcTotalAuthReviewService;
    }
 
    /**
     * Format the readonly config from the given data
     *
     * @param array $data
     * @return array
     */
    public function getConfigFromData(array $data = [])
    {
        $config = ['subSections' => []];

        $isPsv = $this->isPsv($data);

        if ($isPsv) {
            $ocService = $this->psvOperatingCentreReviewService;
            $authService = $this->variationPsvOcTotalAuthReviewService;
        } else {
            $ocService = $this->goodsOperatingCentreReviewService;
            $authService = $this->variationGoodsOcTotalAuthReviewService;
        }

        $added = $updated = $deleted = [];

        foreach ($data['operatingCentres'] as $operatingCentre) {
            switch ($operatingCentre['action']) {
                case 'A':
                    $added[] = $ocService->getConfigFromData($operatingCentre);
                    break;
                case 'U':
                    $updated[] = $ocService->getConfigFromData($operatingCentre);
                    break;
                case 'D':
                    $deleted[] = $ocService->getConfigFromData($operatingCentre);
            }
        }

        if (!empty($added)) {
            $config['subSections'][] = [
                'title' => 'variation-review-operating-centres-added-title',
                'mainItems' => $added
            ];
        }

        if (!empty($updated)) {
            $config['subSections'][] = [
                'title' => 'variation-review-operating-centres-updated-title',
                'mainItems' => $updated
            ];
        }

        if (!empty($deleted)) {
            $config['subSections'][] = [
                'title' => 'variation-review-operating-centres-deleted-title',
                'mainItems' => $deleted
            ];
        }

        $config['subSections'][] = [
            'mainItems' => [
                $authService->getConfigFromData($data)
            ]
        ];

        return $config;
    }
}
