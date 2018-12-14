<?php

namespace Dvsa\OlcsTest\Address\Service;

use Dvsa\Olcs\Address\Service\Address;
use Dvsa\Olcs\Address\Service\Client;
use Dvsa\Olcs\Api\Domain\Repository\AdminAreaTrafficArea;
use Dvsa\Olcs\Api\Domain\Repository\PostcodeEnforcementArea;
use Dvsa\Olcs\Api\Service\Exception;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Http\Response;

/**
 * @covers Dvsa\Olcs\Address\Service\Address
 */
class AddressTest extends MockeryTestCase
{
    /**  @var Client */
    protected $client;

    /**  @var Address */
    protected $sut;

    public function setUp()
    {
        $this->client = m::mock(Client::class);

        $this->sut = new Address($this->client);
    }

    public function testFetchByPostcodeWithResults()
    {
        $postcode = 'AB1 1AB';

        $this->mockClientLookup($postcode, '{"foo": "bar"}');

        $this->assertEquals(['foo' => 'bar'], $this->sut->fetchByPostcode($postcode));
    }

    public function testFetchByPostcodeWithoutResults()
    {
        //  expect
        $this->expectException(Exception::class, Address::ERR_INVALID_RESP_BY_POSTCODE);

        //  call
        $postcode = 'AB1 1AB';

        $this->mockClientLookup($postcode, '', Response::STATUS_CODE_404);

        $this->sut->fetchByPostcode($postcode);
    }

    public function testFetchAdminAreaByPostcodeWithResults()
    {
        $postcode = 'AB1 1AB';

        $this->mockClientLookup($postcode, '[{"administritive_area": 111}]');

        $this->assertEquals(111, $this->sut->fetchAdminAreaByPostcode($postcode));
    }

    public function testFetchAdminAreaByPostcodeWithResultsAlt()
    {
        $postcode = 'AB1 1AB';

        $this->mockClientLookup($postcode, '{"administritive_area": 111}');

        $this->assertEquals(111, $this->sut->fetchAdminAreaByPostcode($postcode));
    }

    public function testFetchAdminAreaByPostcodeWithResultsAlt2()
    {
        $postcode = 'AB1 1AB';

        $this->mockClientLookup($postcode, '{"foo": "bar"}');

        $this->assertNull($this->sut->fetchAdminAreaByPostcode($postcode));
    }

    public function testFetchAdminAreaByPostcodeWithoutResults()
    {
        //  expect
        $this->expectException(Exception::class, Address::ERR_INVALID_RESP_BY_POSTCODE);

        //  call
        $postcode = 'AB1 1AB';

        $this->mockClientLookup($postcode, '', Response::STATUS_CODE_404);

        $this->sut->fetchAdminAreaByPostcode($postcode);
    }

    public function testFetchAdminAreaByPostcodeWithEmptyResults()
    {
        $postcode = 'AB1 1AB';

        $this->mockClientLookup($postcode);

        $this->assertNull($this->sut->fetchAdminAreaByPostcode($postcode));
    }

    public function testFetchTrafficAreaByPostcode()
    {
        $ta = m::mock();

        $adminAreaTrafficArea = m::mock();
        $adminAreaTrafficArea->shouldReceive('getTrafficArea')
            ->once()
            ->andReturn($ta);

        $postcode = 'AB1 1AB';
        $repo = m::mock(AdminAreaTrafficArea::class);
        $repo->shouldReceive('fetchById')
            ->with(111)
            ->andReturn($adminAreaTrafficArea);

        $this->mockClientLookup($postcode, '[{"administritive_area": 111}]');

        $this->assertSame($ta, $this->sut->fetchTrafficAreaByPostcode($postcode, $repo));
    }

    public function testFetchTrafficAreaByPostcodeWithoutRecords()
    {
        //  expect
        $this->expectException(Exception::class, Address::ERR_INVALID_RESP_BY_POSTCODE);

        //  call
        $postcode = 'AB1 1AB';

        /** @var AdminAreaTrafficArea $repo */
        $repo = m::mock(AdminAreaTrafficArea::class);

        $this->mockClientLookup($postcode, '', Response::STATUS_CODE_404);

        $this->sut->fetchTrafficAreaByPostcode($postcode, $repo);
    }

    public function testFetchTrafficAreaByPostcodeWithEmptyResults()
    {
        $postcode = 'AB1 1AB';

        /** @var AdminAreaTrafficArea $repo */
        $repo = m::mock(AdminAreaTrafficArea::class);

        $this->mockClientLookup($postcode);

        $this->assertNull($this->sut->fetchTrafficAreaByPostcode($postcode, $repo));
    }

    public function testFetchEnforcementAreaByPostcodeNoPostcode()
    {
        $postcode = '';
        $repo = m::mock(PostcodeEnforcementArea::class);

        $this->assertEquals(null, $this->sut->fetchEnforcementAreaByPostcode($postcode, $repo));
    }

    public function testFetchEnforcementAreaByPostcodeNoneFound()
    {
        $postcode = 'AB1 1AB';
        $repo = m::mock(PostcodeEnforcementArea::class);
        $repo->shouldReceive('fetchByPostcodeId')
            ->once()
            ->with('AB1 1')
            ->andReturn(null)
            ->shouldReceive('fetchByPostcodeId')
            ->once()
            ->with('AB1')
            ->andReturn(null);

        $this->assertEquals(null, $this->sut->fetchEnforcementAreaByPostcode($postcode, $repo));
    }

    public function testFetchEnforcementAreaByPostcode()
    {
        $ea = m::mock();

        $pae = m::mock();
        $pae->shouldReceive('getEnforcementArea')
            ->andReturn($ea);

        $postcode = 'AB1 1AB';

        $repo = m::mock(PostcodeEnforcementArea::class);
        $repo->shouldReceive('fetchByPostcodeId')
            ->once()
            ->with('AB1 1')
            ->andReturn(null)
            ->shouldReceive('fetchByPostcodeId')
            ->once()
            ->with('AB1')
            ->andReturn($pae);

        $this->assertSame($ea, $this->sut->fetchEnforcementAreaByPostcode($postcode, $repo));
    }

    protected function mockClientLookup($postcode, $content = null, $statusCode = 200)
    {
        /** @var Response $response */
        $response = m::mock(Response::class)->makePartial();
        $response->setStatusCode($statusCode);
        $response->setContent($content);

        $this->client->shouldReceive('setUri')
            ->once()
            ->with('address/' . urlencode($postcode))
            ->shouldReceive('send')
            ->once()
            ->andReturn($response);
    }

    public function testFetchByUprn()
    {
        /** @var Response $response */
        $response = m::mock(Response::class)->makePartial();
        $response->setStatusCode(Response::STATUS_CODE_200);
        $response->setContent('{"foo": "bar"}');

        $this->client->shouldReceive('setUri')
            ->once()
            ->with('address/')
            ->shouldReceive('send')
            ->once()
            ->andReturn($response)
            ->shouldReceive('setParameterGet')
            ->with(['id' => 123])
            ->once();

        $this->assertEquals(['foo' => 'bar'], $this->sut->fetchByUprn(123));
    }

    public function testFetchByUprnNotFound()
    {
        /** @var Response $response */
        $response = m::mock(Response::class)->makePartial();
        $response->setStatusCode(Response::STATUS_CODE_404);
        $response->setContent('');

        $this->expectException(Exception::class, Address::ERR_INVALID_RESP_BY_UPRN);

        $this->client->shouldReceive('setUri')
            ->once()
            ->with('address/')
            ->shouldReceive('send')
            ->once()
            ->andReturn($response)
            ->shouldReceive('setParameterGet')
            ->with(['id' => 123])
            ->once();

        $this->assertFalse($this->sut->fetchByUprn(123));
    }
}
