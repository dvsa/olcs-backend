<?php

/**
 * Application Operating Centres Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Dvsa\Olcs\Snapshot\Service\Snapshots\ApplicationReview\Section;

/**
 * Application Operating Centres Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationOperatingCentresReviewService extends AbstractOperatingCentresReviewService
{
    /** @var PsvOperatingCentreReviewService */
    private $psvOperatingCentreReviewService;

    /** @var ApplicationPsvOcTotalAuthReviewService */
    private $applicationPsvOcTotalAuthReviewService;

    /** @var GoodsOperatingCentreReviewService */
    private $goodsOperatingCentreReviewService;

    /** @var ApplicationGoodsOcTotalAuthReviewService */
    private $applicationGoodsOcTotalAuthReviewService;

    /** @var TrafficAreaReviewService */
    private $trafficAreaReviewService;

    /**
     * Create service instance
     *
     * @param AbstractReviewServiceServices $abstractReviewServiceServices
     * @param PsvOperatingCentreReviewService $psvOperatingCentreReviewService
     * @param ApplicationPsvOcTotalAuthReviewService $applicationPsvOcTotalAuthReviewService
     * @param GoodsOperatingCentreReviewService $goodsOperatingCentreReviewService
     * @param ApplicationGoodsOcTotalAuthReviewService $applicationGoodsOcTotalAuthReviewService
     * @param TrafficAreaReviewService $trafficAreaReviewService
     *
     * @return ApplicationOperatingCentresReviewService
     */
    public function __construct(
        AbstractReviewServiceServices $abstractReviewServiceServices,
        PsvOperatingCentreReviewService $psvOperatingCentreReviewService,
        ApplicationPsvOcTotalAuthReviewService $applicationPsvOcTotalAuthReviewService,
        GoodsOperatingCentreReviewService $goodsOperatingCentreReviewService,
        ApplicationGoodsOcTotalAuthReviewService $applicationGoodsOcTotalAuthReviewService,
        TrafficAreaReviewService $trafficAreaReviewService
    ) {
        parent::__construct($abstractReviewServiceServices);
        $this->psvOperatingCentreReviewService = $psvOperatingCentreReviewService;
        $this->applicationPsvOcTotalAuthReviewService = $applicationPsvOcTotalAuthReviewService;
        $this->goodsOperatingCentreReviewService = $goodsOperatingCentreReviewService;
        $this->applicationGoodsOcTotalAuthReviewService = $applicationGoodsOcTotalAuthReviewService;
        $this->trafficAreaReviewService = $trafficAreaReviewService;
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
            $authService = $this->applicationPsvOcTotalAuthReviewService;
        } else {
            $ocService = $this->goodsOperatingCentreReviewService;
            $authService = $this->applicationGoodsOcTotalAuthReviewService;
        }

        $added = [];

        foreach ($data['operatingCentres'] as $operatingCentre) {
            $added[] = $ocService->getConfigFromData($operatingCentre);
        }

        if (!empty($added)) {
            $config['subSections'][] = ['mainItems' => $added];
        }

        $config['subSections'][] = [
            'mainItems' => [
                $this->trafficAreaReviewService->getConfigFromData($data),
                $authService->getConfigFromData($data)
            ]
        ];

        return $config;
    }
}
