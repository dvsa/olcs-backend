<?php

/**
 * Variation Undertakings Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Snapshot\Service\Snapshots\ApplicationReview\Section;

use OlcsTest\Bootstrap;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\Olcs\Snapshot\Service\Snapshots\ApplicationReview\Section\VariationUndertakingsReviewService;
use Dvsa\Olcs\Api\Entity\Licence\Licence;

/**
 * Variation Undertakings Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationUndertakingsReviewServiceTest extends MockeryTestCase
{
    protected $sut;
    protected $sm;

    public function setUp()
    {
        $this->sut = new VariationUndertakingsReviewService();

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
                    'licenceType' => ['id' => Licence::LICENCE_TYPE_STANDARD_NATIONAL],
                    'isInternal' => true
                ],
                [
                    'markup' => 'markup-application_undertakings_PSV430-translated'
                ]
            ],
            [
                [
                    'isGoods' => true,
                    'licence' => [
                        'licenceType' => [
                            'id' => Licence::LICENCE_TYPE_RESTRICTED
                        ]
                    ],
                    'licenceType' => [
                        'id' => Licence::LICENCE_TYPE_STANDARD_NATIONAL
                    ],
                    'niFlag' => 'Y',
                    'isInternal' => true
                ],
                [
                    'markup' => 'markup-application_undertakings_GV80A-NI-translated'
                ]
            ],
            [
                [
                    'isGoods' => true,
                    'licence' => [
                        'licenceType' => [
                            'id' => Licence::LICENCE_TYPE_RESTRICTED
                        ]
                    ],
                    'licenceType' => [
                        'id' => Licence::LICENCE_TYPE_STANDARD_NATIONAL
                    ],
                    'niFlag' => 'N',
                    'isInternal' => true
                ],
                [
                    'markup' => 'markup-application_undertakings_GV80A-translated'
                ]
            ],
            [
                [
                    'isGoods' => true,
                    'licence' => [
                        'licenceType' => [
                            'id' => Licence::LICENCE_TYPE_STANDARD_NATIONAL
                        ]
                    ],
                    'licenceType' => [
                        'id' => Licence::LICENCE_TYPE_STANDARD_NATIONAL
                    ],
                    'niFlag' => 'Y',
                    'isInternal' => true
                ],
                [
                    'markup' => 'markup-application_undertakings_GV81-NI-translated'
                ]
            ],
            [
                [
                    'isGoods' => true,
                    'licence' => [
                        'licenceType' => [
                            'id' => Licence::LICENCE_TYPE_STANDARD_NATIONAL
                        ]
                    ],
                    'licenceType' => [
                        'id' => Licence::LICENCE_TYPE_STANDARD_NATIONAL
                    ],
                    'niFlag' => 'N',
                    'isInternal' => true
                ],
                [
                    'markup' => 'markup-application_undertakings_GV81-translated'
                ]
            ]
        ];
    }
}
