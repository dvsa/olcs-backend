<?php

namespace Dvsa\OlcsTest\Snapshot\Service\Snapshots\Surrender;

use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Surrender;
use Dvsa\Olcs\Snapshot\Service\Snapshots\Surrender\Generator;
use Dvsa\Olcs\Snapshot\Service\Snapshots\Surrender\Section\CommunityLicenceReviewService;
use Dvsa\Olcs\Snapshot\Service\Snapshots\Surrender\Section\CurrentDiscsReviewService;
use Dvsa\Olcs\Snapshot\Service\Snapshots\Surrender\Section\DeclarationReviewService;
use Dvsa\Olcs\Snapshot\Service\Snapshots\Surrender\Section\OperatorLicenceReviewService;
use Dvsa\Olcs\Snapshot\Service\Snapshots\Surrender\Section\LicenceDetailsService;
use Dvsa\Olcs\Snapshot\Service\Snapshots\Surrender\Section\SignatureReviewService;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Model\ViewModel;

class GeneratorTest extends MockeryTestCase
{
    /**
     * @var Generator
     */
    protected $sut;

    /**
     * @var array
     */
    protected $services;

    protected function setUp(): void
    {
        $sm = m::mock(ServiceLocatorInterface::class);

        $this->services = [
            LicenceDetailsService::class => m::mock(),
            CurrentDiscsReviewService::class => m::mock(),
            OperatorLicenceReviewService::class => m::mock(),
            CommunityLicenceReviewService::class => m::mock(),
            DeclarationReviewService::class => m::mock(),
            SignatureReviewService::class => m::mock(),
            'ViewRenderer' => m::mock()
        ];

        $sm->shouldReceive('get')->andReturnUsing(
            function ($key) {
                return $this->services[$key];
            }
        );
        $this->sut = new Generator();
        $this->sut->setServiceLocator($sm);
    }

    /**
     * @dataProvider dbLicenceType
     */
    public function testGenerate($licenceType, $expected)
    {
        $licence = m::mock(Licence::class);
        $licence->shouldReceive('getLicNo')->andReturn('AB1234567');
        $licence->shouldReceive('getLicenceType->getId')->andReturn($licenceType);

        $surrender = m::mock(Surrender::class);
        $surrender->shouldReceive('getLicence')->andReturn($licence);


        $this->setServices($surrender);

        $this->services['ViewRenderer']->shouldReceive('render')
            ->once()
            ->with(m::type(ViewModel::class))
            ->andReturnUsing(
                function ($view) {
                    return $view;
                }
            );

        /** @var ViewModel $result */
        $result = $this->sut->generate($surrender);

        $this->assertInstanceOf(ViewModel::class, $result);
        $this->assertEquals('layout/review', $result->getTemplate());

        $variables = $result->getVariables();

        $this->assertSame($expected, $variables);
    }

    protected function setServices($surrender)
    {
        $this->services[LicenceDetailsService::class]->shouldReceive('getConfigFromData')
            ->once()->with($surrender)->andReturn('licenceDetails');

        $this->services[CurrentDiscsReviewService::class]->shouldReceive('getConfigFromData')
            ->once()->with($surrender)->andReturn('currentDiscs');

        $this->services[OperatorLicenceReviewService::class]->shouldReceive('getConfigFromData')
            ->once()->with($surrender)->andReturn('Operator licence');

        $this->services[CommunityLicenceReviewService::class]->shouldReceive('getConfigFromData')
            ->once()->with($surrender)->andReturn('Community licence');

        $this->services[DeclarationReviewService::class]->shouldReceive('getConfigFromData')
            ->once()->with($surrender)->andReturn('declaration');

        $this->services[SignatureReviewService::class]->shouldReceive('getConfigFromData')
            ->once()->with($surrender)->andReturn('signature');
    }

    public function dbLicenceType()
    {
        return [
            [
                Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL,
                [
                    'reviewTitle' => 'surrender-review-title',
                    'subTitle' => 'AB1234567',
                    'settings' => [
                        'hide-count' => true
                    ],
                    'sections' => [
                        [
                            'header' => 'surrender-review-licence',
                            'config' => 'licenceDetails'
                        ],
                        [
                            'header' => 'surrender-review-current-discs',
                            'config' => 'currentDiscs'
                        ],
                        [
                            'header' => 'surrender-review-operator-licence',
                            'config' => 'Operator licence'
                        ],
                        [
                            'header' => 'surrender-review-community-licence',
                            'config' => 'Community licence'
                        ],
                        [
                            'header' => 'surrender-review-declaration',
                            'config' => 'declaration'
                        ],
                        [
                            'config' => 'signature'
                        ]
                    ]
                ],

                [
                    Licence::LICENCE_TYPE_STANDARD_NATIONAL,
                    [
                        'reviewTitle' => 'surrender-review-title',
                        'subTitle' => 'AB1234567',
                        'settings' => [
                            'hide-count' => true
                        ],
                        'sections' => [
                            [
                                'header' => 'surrender-review-licence',
                                'config' => 'licenceDetails'
                            ],
                            [
                                'header' => 'surrender-review-current-discs',
                                'config' => 'currentDiscs'
                            ],
                            [
                                'header' => 'surrender-review-operator-licence',
                                'config' => 'Operator licence'
                            ],
                            [
                                'header' => 'surrender-review-declaration',
                                'config' => 'declaration'
                            ],
                            [
                                'config' => 'signature'
                            ]
                        ]
                    ],
                ]
            ]
        ];
    }
}
