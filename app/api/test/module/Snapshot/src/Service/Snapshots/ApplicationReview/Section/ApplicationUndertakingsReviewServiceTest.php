<?php

/**
 * Application Undertakings Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Snapshot\Service\Snapshots\ApplicationReview\Section;

use OlcsTest\Bootstrap;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\Olcs\Snapshot\Service\Snapshots\ApplicationReview\Section\ApplicationUndertakingsReviewService;
use Dvsa\Olcs\Api\Entity\Licence\Licence;

/**
 * Application Undertakings Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationUndertakingsReviewServiceTest extends MockeryTestCase
{
    /** @var ApplicationUndertakingsReviewService */
    protected $sut;
    protected $sm;

    public function setUp(): void
    {
        $this->sut = new ApplicationUndertakingsReviewService();

        $this->sm = Bootstrap::getServiceManager();
        $this->sut->setServiceLocator($this->sm);
    }

    /**
     * @dataProvider providerGetConfigFromData
     */
    public function testGetConfigFromData($data, $expected)
    {
        $mockTranslator = m::mock();
        $this->sm->setService('translator', $mockTranslator);

        $mockTranslator->shouldReceive('translate')
            ->andReturnUsing(
                function ($string) {
                    return $string . '-translated';
                }
            );

        $this->assertEquals($expected, $this->sut->getConfigFromData($data));
    }

    public function providerGetConfigFromData()
    {
        return [
            [
                [
                    'isGoods' => false,
                    'licenceType' => ['id' => Licence::LICENCE_TYPE_SPECIAL_RESTRICTED],
                    'isInternal' => true,
                    'licence' => [
                        'organisation' => [
                            'type' => [
                                'id' => 'org_t_rc'
                            ]
                        ]
                    ],
                    'niFlag' => 'N'
                ],
                [
                    'markup' => 'markup-application_undertakings_PSV356-translated'
                ]
            ],
            [
                [
                    'isGoods' => false,
                    'licenceType' => ['id' => Licence::LICENCE_TYPE_STANDARD_NATIONAL],
                    'isInternal' => true,
                    'licence' => [
                        'organisation' => [
                            'type' => [
                                'id' => 'org_t_rc'
                            ]
                        ]
                    ],
                    'niFlag' => 'N'
                ],
                [
                    'markup' => 'markup-application_undertakings_PSV421-translated'
                ]
            ],
            [
                [
                    'isGoods' => true,
                    'niFlag' => 'Y',
                    'licenceType' => ['id' => Licence::LICENCE_TYPE_STANDARD_NATIONAL],
                    'isInternal' => true,
                    'licence' => [
                        'organisation' => [
                            'type' => [
                                'id' => 'org_t_rc'
                            ]
                        ]
                    ],
                ],
                [
                    'markup' => 'markup-application_undertakings_GV79-NI-translated'
                ]
            ],
            [
                [
                    'isGoods' => true,
                    'niFlag' => 'N',
                    'licenceType' => ['id' => Licence::LICENCE_TYPE_STANDARD_NATIONAL],
                    'isInternal' => true,
                    'licence' => [
                        'organisation' => [
                            'type' => [
                                'id' => 'org_t_rc'
                            ]
                        ]
                    ],
                ],
                [
                    'markup' => 'markup-application_undertakings_GV79-translated'
                ]
            ]
        ];
    }

    /**
     * @dataProvider providerGetConfigFromData
     *
     * Use the same data set as "testGetConfigFromData" to test we get the same results
     */
    public function testGetMarkupForLicence($data, $expected)
    {
        $mockTranslator = m::mock();
        $this->sm->setService('translator', $mockTranslator);

        $mockTranslator->shouldReceive('translate')
            ->andReturnUsing(
                function ($string) {
                    return $string . '-translated';
                }
            );

        $mockLicence = m::mock(Licence::class);
        $mockLicence->shouldReceive('getLicenceType->getId')->with()->once()->andReturn($data['licenceType']['id']);
        $mockLicence->shouldReceive('getOrganisation->getType->getId')->with()->once()->andReturn(
            $data['licence']['organisation']['type']['id']
        );
        $mockLicence->shouldReceive('isGoods')->with()->once()->andReturn($data['isGoods']);
        $mockLicence->shouldReceive('getTrafficArea->getIsNi')->with()->once()->andReturn($data['niFlag'] === 'Y');

        $this->assertEquals($expected['markup'], $this->sut->getMarkupForLicence($mockLicence, $data['isInternal']));
    }
}
