<?php

/**
 * Variation Operating Centre Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\VariationOperatingCentre;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\QueryHandler\VariationOperatingCentre\VariationOperatingCentre;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Transfer\Query\VariationOperatingCentre\VariationOperatingCentre as Qry;
use Dvsa\Olcs\Transfer\Query\ApplicationOperatingCentre\ApplicationOperatingCentre;
use Dvsa\Olcs\Transfer\Query\LicenceOperatingCentre\LicenceOperatingCentre;

/**
 * Variation Operating Centre Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationOperatingCentreTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new VariationOperatingCentre();

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create(['id' => 'A111']);

        $response = m::mock();
        $response->shouldReceive('setValue')
            ->once()
            ->with('canUpdateAddress', false);

        $this->queryHandler->shouldReceive('handleQuery')
            ->andReturnUsing(
                function ($dto) use ($response) {

                    $this->assertInstanceOf(ApplicationOperatingCentre::class, $dto);
                    $data = $dto->getArrayCopy();

                    $this->assertEquals(111, $data['id']);

                    return $response;
                }
            );

        $this->assertEquals($response, $this->sut->handleQuery($query));
    }

    public function testHandleQueryLicence()
    {
        $query = Qry::create(['id' => 'L111']);

        $response = m::mock();
        $response->shouldReceive('setValue')
            ->once()
            ->with('canUpdateAddress', false);

        $this->queryHandler->shouldReceive('handleQuery')
            ->andReturnUsing(
                function ($dto) use ($response) {

                    $this->assertInstanceOf(LicenceOperatingCentre::class, $dto);
                    $data = $dto->getArrayCopy();

                    $this->assertEquals(111, $data['id']);

                    return $response;
                }
            );

        $this->assertEquals($response, $this->sut->handleQuery($query));
    }

    public function testHandleQueryNeither()
    {
        $this->setExpectedException(\Exception::class);

        $query = Qry::create(['id' => 'X111']);

        $this->assertEquals('RESPONSE', $this->sut->handleQuery($query));
    }
}
