<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\IrhpPermit;

use Dvsa\Olcs\Api\Domain\QueryHandlerManager;
use Dvsa\Olcs\Api\Domain\QueryHandler\IrhpPermit\UniqueCountriesByLicence as Handler;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;
use Dvsa\Olcs\Transfer\Query\IrhpPermit\UniqueCountriesByLicence as UniqueCountriesByLicenceQuery;
use Dvsa\Olcs\Transfer\Query\IrhpPermit\GetListByLicence as GetListByLicenceQuery;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;

/**
 * UniqueCountriesByLicence Test
 */
class UniqueCountriesByLicenceTest extends QueryHandlerTestCase
{
    private $query;

    public function setUp()
    {
        $this->sut = new Handler();

        $this->mockedSmServices['QueryHandlerManager'] = m::mock(QueryHandlerManager::class);

        $this->query = m::mock(QueryInterface::class);

        parent::setUp();
    }

    public function testHandleQueryBilateral()
    {
        $licenceId = 797;
        $irhpPermitTypeId = IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL;

        $this->query->shouldReceive('getLicence')
            ->withNoArgs()
            ->andReturn($licenceId);
        $this->query->shouldReceive('getIrhpPermitType')
            ->withNoArgs()
            ->andReturn($irhpPermitTypeId);

        $arrayCopy = [
            'licence' => $licenceId,
            'irhpPermitType' => $irhpPermitTypeId,
        ];

        $this->query->shouldReceive('getArrayCopy')
            ->withNoArgs()
            ->andReturn($arrayCopy);

        $getListByLicenceResults = [
            'results' => [
                [
                    'irhpPermitRange' => [
                        'irhpPermitStock' => [
                            'country' => [
                                'id' => 'SE',
                                'countryDesc' => 'Sweden'
                            ]
                        ]
                    ]
                ],
                [
                    'irhpPermitRange' => [
                        'irhpPermitStock' => [
                            'country' => [
                                'id' => 'DE',
                                'countryDesc' => 'Germany'
                            ]
                        ]
                    ]
                ],
                [
                    'irhpPermitRange' => [
                        'irhpPermitStock' => [
                            'country' => [
                                'id' => 'NO',
                                'countryDesc' => 'Norway'
                            ]
                        ]
                    ]
                ],
                [
                    'irhpPermitRange' => [
                        'irhpPermitStock' => [
                            'country' => [
                                'id' => 'NO',
                                'countryDesc' => 'Norway'
                            ]
                        ]
                    ]
                ],
                [
                    'irhpPermitRange' => [
                        'irhpPermitStock' => [
                            'country' => [
                                'id' => 'DE',
                                'countryDesc' => 'Germany'
                            ]
                        ]
                    ]
                ],
            ]
        ];

        $this->mockedSmServices['QueryHandlerManager']->shouldReceive('handleQuery')
            ->with(m::type(GetListByLicenceQuery::class), false)
            ->andReturnUsing(function ($query) use (
                $licenceId,
                $irhpPermitTypeId,
                $getListByLicenceResults
            ) {
                $this->assertEquals($licenceId, $query->getLicence());
                $this->assertEquals($irhpPermitTypeId, $query->getIrhpPermitType());

                return $getListByLicenceResults;
            });

        $expected = [
            'DE' => 'Germany',
            'NO' => 'Norway',
            'SE' => 'Sweden',
        ];

        $this->assertEquals(
            $expected,
            $this->sut->handleQuery($this->query)
        );
    }

    /**
     * @dataProvider dpHandleQueryNonBilateral
     */
    public function testHandleQueryNonBilateral($irhpPermitTypeId)
    {
        $this->query->shouldReceive('getIrhpPermitType')
            ->withNoArgs()
            ->andReturn($irhpPermitTypeId);

        $expected = [];

        $this->assertEquals(
            $expected,
            $this->sut->handleQuery($this->query)
        );
    }

    public function dpHandleQueryNonBilateral()
    {
        return [
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_REMOVAL],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_MULTILATERAL],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_CERT_ROADWORTHINESS_VEHICLE],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_CERT_ROADWORTHINESS_TRAILER],
        ];
    }
}
