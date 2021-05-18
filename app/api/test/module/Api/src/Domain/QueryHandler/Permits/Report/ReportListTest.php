<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Permits\Report;

use Dvsa\Olcs\Api\Domain\QueryHandler\Permits\Report\ReportList;
use Dvsa\Olcs\Api\Domain\Service\PermitsReportService;
use Dvsa\Olcs\Transfer\Query\Permits\ReportList as Qry;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;

class ReportListTest extends QueryHandlerTestCase
{
    /**
     * @test
     */
    public function handleQuery_IsCallable()
    {
        $this->assertIsCallable([$this->sut, 'handleQuery']);
    }

    /**
     * @test
     * @depends handleQuery_IsCallable
     */
    public function handleQuery_ReturnsArrayFormat()
    {
        $result = $this->sut->handleQuery(Qry::create([]));

        $this->assertIsArray($result);
        $this->assertArrayHasKey('result', $result);
        $this->assertArrayHasKey('count', $result);
    }

    /**
     * @test
     * @depends handleQuery_ReturnsArrayFormat
     */
    public function handleQuery_ReturnsListOfAvailableReportsFromPermitReportService()
    {
        $result = $this->sut->handleQuery(Qry::create([]));
        $this->assertEquals(PermitsReportService::REPORT_TYPES, $result['result']);
    }

    /**
     * @test
     * @depends handleQuery_ReturnsListOfAvailableReportsFromPermitReportService
     */
    public function handleQuery_ReturnsValidCountOfAvailableReportsFromPermitReportService()
    {
        $result = $this->sut->handleQuery(Qry::create([]));
        $this->assertEquals(count(PermitsReportService::REPORT_TYPES), $result['count']);
    }

    public function setUp(): void
    {
        $this->sut = new ReportList();
        parent::setUp();
    }
}
