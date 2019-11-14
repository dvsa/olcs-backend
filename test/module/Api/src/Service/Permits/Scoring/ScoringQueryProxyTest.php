<?php

namespace Dvsa\OlcsTest\Api\Service\Permits\Scoring;

use Dvsa\Olcs\Api\Domain\RepositoryServiceManager;
use Dvsa\Olcs\Api\Domain\Repository\AbstractScoringRepository;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitStock as IrhpPermitStockRepository;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Service\Permits\Scoring\ScoringQueryProxy;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * ScoringQueryProxyTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class ScoringQueryProxyTest extends MockeryTestCase
{
    private $stockId = 47;

    private $queryResult;

    private $irhpPermitStock;

    private $irhpPermitStockRepo;

    private $applicationRepo;

    private $repoServiceManager;

    private $scoringQueryProxy;

    public function setUp()
    {
        $this->queryResult = [
            'item1' => 'value1',
            'item2' => 'value2',
        ];

        $this->irhpPermitStock = m::mock(IrhpPermitStock::class);

        $this->irhpPermitStockRepo = m::mock(IrhpPermitStockRepository::class);
        $this->irhpPermitStockRepo->shouldReceive('fetchById')
            ->with($this->stockId)
            ->andReturn($this->irhpPermitStock);

        $this->applicationRepo = m::mock(AbstractScoringRepository::class);

        $this->repoServiceManager = m::mock(RepositoryServiceManager::class);
        $this->repoServiceManager->shouldReceive('get')
            ->with('IrhpPermitStock')
            ->andReturn($this->irhpPermitStockRepo);

        $this->scoringQueryProxy = new ScoringQueryProxy($this->repoServiceManager);
    }

    /**
     * @dataProvider dpIsEcmtAnnualToRepoName
     */
    public function testFetchApplicationIdsAwaitingScoring($isEcmtAnnual, $expectedRepoName)
    {
        $this->irhpPermitStock->shouldReceive('getIrhpPermitType->isEcmtAnnual')
            ->andReturn($isEcmtAnnual);

        $this->applicationRepo->shouldReceive('fetchApplicationIdsAwaitingScoring')
            ->with($this->stockId)
            ->once()
            ->andReturn($this->queryResult);

        $this->repoServiceManager->shouldReceive('get')
            ->with($expectedRepoName)
            ->andReturn($this->applicationRepo);

        $this->assertEquals(
            $this->queryResult,
            $this->scoringQueryProxy->fetchApplicationIdsAwaitingScoring($this->stockId)
        );
    }

    /**
     * @dataProvider dpIsEcmtAnnualToRepoName
     */
    public function testFetchInScopeUnderConsiderationApplicationIds($isEcmtAnnual, $expectedRepoName)
    {
        $this->irhpPermitStock->shouldReceive('getIrhpPermitType->isEcmtAnnual')
            ->andReturn($isEcmtAnnual);

        $this->applicationRepo->shouldReceive('fetchInScopeUnderConsiderationApplicationIds')
            ->with($this->stockId)
            ->once()
            ->andReturn($this->queryResult);

        $this->repoServiceManager->shouldReceive('get')
            ->with($expectedRepoName)
            ->andReturn($this->applicationRepo);

        $this->assertEquals(
            $this->queryResult,
            $this->scoringQueryProxy->fetchInScopeUnderConsiderationApplicationIds($this->stockId)
        );
    }

    /**
     * @dataProvider dpHasInScopeUnderConsiderationApplications
     */
    public function testHasInScopeUnderConsiderationApplications($applicationIds, $expected)
    {
        $scoringQueryProxy = m::mock(ScoringQueryProxy::class)->makePartial();

        $scoringQueryProxy->shouldReceive('fetchInScopeUnderConsiderationApplicationIds')
            ->with($this->stockId)
            ->andReturn($applicationIds);

        $this->assertEquals(
            $expected,
            $scoringQueryProxy->hasInScopeUnderConsiderationApplications($this->stockId)
        );
    }

    public function dpHasInScopeUnderConsiderationApplications()
    {
        return [
            [
                [],
                false
            ],
            [
                [5],
                true
            ],
            [
                [5, 10],
                true
            ],
        ];
    }

    /**
     * @dataProvider dpIsEcmtAnnualToRepoName
     */
    public function testClearScope($isEcmtAnnual, $expectedRepoName)
    {
        $this->irhpPermitStock->shouldReceive('getIrhpPermitType->isEcmtAnnual')
            ->andReturn($isEcmtAnnual);

        $this->applicationRepo->shouldReceive('clearScope')
            ->with($this->stockId)
            ->once();

        $this->repoServiceManager->shouldReceive('get')->with($expectedRepoName)
            ->andReturn($this->applicationRepo);

        $this->scoringQueryProxy->clearScope($this->stockId);
    }

    /**
     * @dataProvider dpIsEcmtAnnualToRepoName
     */
    public function testApplyScope($isEcmtAnnual, $expectedRepoName)
    {
        $this->irhpPermitStock->shouldReceive('getIrhpPermitType->isEcmtAnnual')
            ->andReturn($isEcmtAnnual);

        $this->applicationRepo->shouldReceive('applyScope')
            ->with($this->stockId)
            ->once();

        $this->repoServiceManager->shouldReceive('get')->with($expectedRepoName)
            ->andReturn($this->applicationRepo);

        $this->scoringQueryProxy->applyScope($this->stockId);
    }

    /**
     * @dataProvider dpIsEcmtAnnualToRepoName
     */
    public function testGetScoreOrderedBySectorInScope($isEcmtAnnual, $expectedRepoName)
    {
        $sectorsId = 7;

        $this->irhpPermitStock->shouldReceive('getIrhpPermitType->isEcmtAnnual')
            ->andReturn($isEcmtAnnual);

        $this->applicationRepo->shouldReceive('getScoreOrderedBySectorInScope')
            ->with($this->stockId, $sectorsId)
            ->once()
            ->andReturn($this->queryResult);

        $this->repoServiceManager->shouldReceive('get')
            ->with($expectedRepoName)
            ->andReturn($this->applicationRepo);

        $this->assertEquals(
            $this->queryResult,
            $this->scoringQueryProxy->getScoreOrderedBySectorInScope($this->stockId, $sectorsId)
        );
    }

    /**
     * @dataProvider dpIsEcmtAnnualToRepoName
     */
    public function testGetSuccessfulDaCountInScope($isEcmtAnnual, $expectedRepoName)
    {
        $jurisdictionId = 'D';

        $this->irhpPermitStock->shouldReceive('getIrhpPermitType->isEcmtAnnual')
            ->andReturn($isEcmtAnnual);

        $this->applicationRepo->shouldReceive('getSuccessfulDaCountInScope')
            ->with($this->stockId, $jurisdictionId)
            ->once()
            ->andReturn($this->queryResult);

        $this->repoServiceManager->shouldReceive('get')
            ->with($expectedRepoName)
            ->andReturn($this->applicationRepo);

        $this->assertEquals(
            $this->queryResult,
            $this->scoringQueryProxy->getSuccessfulDaCountInScope($this->stockId, $jurisdictionId)
        );
    }

    /**
     * @dataProvider dpIsEcmtAnnualToRepoName
     */
    public function testGetUnsuccessfulScoreOrderedInScope($isEcmtAnnual, $expectedRepoName)
    {
        $trafficAreaId = 'W';

        $this->irhpPermitStock->shouldReceive('getIrhpPermitType->isEcmtAnnual')
            ->andReturn($isEcmtAnnual);

        $this->applicationRepo->shouldReceive('getUnsuccessfulScoreOrderedInScope')
            ->with($this->stockId, $trafficAreaId)
            ->once()
            ->andReturn($this->queryResult);

        $this->repoServiceManager->shouldReceive('get')
            ->with($expectedRepoName)
            ->andReturn($this->applicationRepo);

        $this->assertEquals(
            $this->queryResult,
            $this->scoringQueryProxy->getUnsuccessfulScoreOrderedInScope($this->stockId, $trafficAreaId)
        );
    }

    /**
     * @dataProvider dpIsEcmtAnnualToRepoName
     */
    public function testGetSuccessfulCountInScope($isEcmtAnnual, $expectedRepoName)
    {
        $assignedEmissionsCategoryId = RefData::EMISSIONS_CATEGORY_EURO5_REF;

        $this->irhpPermitStock->shouldReceive('getIrhpPermitType->isEcmtAnnual')
            ->andReturn($isEcmtAnnual);

        $this->applicationRepo->shouldReceive('getSuccessfulCountInScope')
            ->with($this->stockId, $assignedEmissionsCategoryId)
            ->once()
            ->andReturn($this->queryResult);

        $this->repoServiceManager->shouldReceive('get')
            ->with($expectedRepoName)
            ->andReturn($this->applicationRepo);

        $this->assertEquals(
            $this->queryResult,
            $this->scoringQueryProxy->getSuccessfulCountInScope($this->stockId, $assignedEmissionsCategoryId)
        );
    }

    /**
     * @dataProvider dpIsEcmtAnnualToRepoName
     */
    public function testGetSuccessfulCountInScopeNull($isEcmtAnnual, $expectedRepoName)
    {
        $this->irhpPermitStock->shouldReceive('getIrhpPermitType->isEcmtAnnual')
            ->andReturn($isEcmtAnnual);

        $this->applicationRepo->shouldReceive('getSuccessfulCountInScope')
            ->with($this->stockId, null)
            ->once()
            ->andReturn($this->queryResult);

        $this->repoServiceManager->shouldReceive('get')
            ->with($expectedRepoName)
            ->andReturn($this->applicationRepo);

        $this->assertEquals(
            $this->queryResult,
            $this->scoringQueryProxy->getSuccessfulCountInScope($this->stockId)
        );
    }

    /**
     * @dataProvider dpIsEcmtAnnualToRepoName
     */
    public function testGetSuccessfulScoreOrderedInScope($isEcmtAnnual, $expectedRepoName)
    {
        $this->irhpPermitStock->shouldReceive('getIrhpPermitType->isEcmtAnnual')
            ->andReturn($isEcmtAnnual);

        $this->applicationRepo->shouldReceive('getSuccessfulScoreOrderedInScope')
            ->with($this->stockId)
            ->once()
            ->andReturn($this->queryResult);

        $this->repoServiceManager->shouldReceive('get')
            ->with($expectedRepoName)
            ->andReturn($this->applicationRepo);

        $this->assertEquals(
            $this->queryResult,
            $this->scoringQueryProxy->getSuccessfulScoreOrderedInScope($this->stockId)
        );
    }

    /**
     * @dataProvider dpIsEcmtAnnualToRepoName
     */
    public function testFetchApplicationIdToCountryIdAssociations($isEcmtAnnual, $expectedRepoName)
    {
        $this->irhpPermitStock->shouldReceive('getIrhpPermitType->isEcmtAnnual')
            ->andReturn($isEcmtAnnual);

        $this->applicationRepo->shouldReceive('fetchApplicationIdToCountryIdAssociations')
            ->with($this->stockId)
            ->once()
            ->andReturn($this->queryResult);

        $this->repoServiceManager->shouldReceive('get')
            ->with($expectedRepoName)
            ->andReturn($this->applicationRepo);

        $this->assertEquals(
            $this->queryResult,
            $this->scoringQueryProxy->fetchApplicationIdToCountryIdAssociations($this->stockId)
        );
    }

    /**
     * @dataProvider dpIsEcmtAnnualToRepoName
     */
    public function testFetchScoringReport($isEcmtAnnual, $expectedRepoName)
    {
        $this->irhpPermitStock->shouldReceive('getIrhpPermitType->isEcmtAnnual')
            ->andReturn($isEcmtAnnual);

        $this->applicationRepo->shouldReceive('fetchScoringReport')
            ->with($this->stockId)
            ->once()
            ->andReturn($this->queryResult);

        $this->repoServiceManager->shouldReceive('get')
            ->with($expectedRepoName)
            ->andReturn($this->applicationRepo);

        $this->assertEquals(
            $this->queryResult,
            $this->scoringQueryProxy->fetchScoringReport($this->stockId)
        );
    }

    /**
     * @dataProvider dpIsEcmtAnnualToRepoName
     */
    public function testFetchDeviationSourceValues($isEcmtAnnual, $expectedRepoName)
    {
        $this->irhpPermitStock->shouldReceive('getIrhpPermitType->isEcmtAnnual')
            ->andReturn($isEcmtAnnual);

        $this->applicationRepo->shouldReceive('fetchDeviationSourceValues')
            ->with($this->stockId)
            ->once()
            ->andReturn($this->queryResult);

        $this->repoServiceManager->shouldReceive('get')
            ->with($expectedRepoName)
            ->andReturn($this->applicationRepo);

        $this->assertEquals(
            $this->queryResult,
            $this->scoringQueryProxy->fetchDeviationSourceValues($this->stockId)
        );
    }

    public function dpIsEcmtAnnualToRepoName()
    {
        return [
            [true, 'EcmtPermitApplication'],
            [false, 'IrhpApplication'],
        ];
    }
}
