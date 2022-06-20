<?php

/**
 * Generator Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Snapshot\Service\Snapshots\ApplicationReview;

use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\Application\ApplicationCompletion;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Snapshot\Service\Snapshots\ApplicationReview\Section\SignatureReviewService;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use OlcsTest\Bootstrap;
use Dvsa\Olcs\Snapshot\Service\Snapshots\ApplicationReview\Generator;
use Dvsa\Olcs\Snapshot\Service\Snapshots\AbstractGeneratorServices;
use Dvsa\Olcs\Api\Service\Lva\SectionAccessService;
use Dvsa\Olcs\Utils\Translation\NiTextTranslation;
use Laminas\View\Model\ViewModel;
use Laminas\View\Renderer\PhpRenderer;

/**
 * Generator Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class GeneratorTest extends MockeryTestCase
{
    /**
     * @var Generator
     */
    protected $sut;

    /**
     * @var \Mockery\MockInterface|SectionAccessService
     */
    protected $sectionAccessService;

    /**
     * @var \Mockery\MockInterface|PhpRenderer
     */
    protected $viewRenderer;

    /**
     * @var \Mockery\MockInterface|SignatureReviewService
     */
    protected $mockSignature;

    /**
     * @var Application
     */
    protected $application;

    /**
     * @var \Mockery\MockInterface|NiTextTranslation
     */
    protected $niTranslation;

    public function setUp(): void
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->niTranslation = m::mock(NiTextTranslation::class);

        $this->sectionAccessService = m::mock(SectionAccessService::class);

        $this->viewRenderer = m::mock(PhpRenderer::class);

        $abstractGeneratorServices = m::mock(AbstractGeneratorServices::class);
        $abstractGeneratorServices->shouldReceive('getRenderer')
            ->withNoArgs()
            ->andReturn($this->viewRenderer);

        $this->mockSignature = m::mock(SignatureReviewService::class);

        $this->application = m::mock(Application::class)->makePartial();

        $this->sut = new Generator(
            $abstractGeneratorServices,
            $this->sectionAccessService,
            $this->niTranslation,
            $this->mockSignature,
            $this->sm
        );
    }

    public function testGenerateVariation()
    {
        $expectedData = [
            'sections' => [
                'vehicles'
            ],
            'id' => 111,
            'isGoods' => true,
            'isSpecialRestricted' => false,
            'isInternal' => true,
            'licence' => [
                'organisation' => [
                    'name' => 'Foo ltd'
                ],
                'licNo' => 'AB12345678'
            ]
        ];

        $mockVehicles = m::mock();
        $mockVehicles->shouldReceive('getConfigFromData')
            ->with($expectedData)
            ->andReturn(['bar' => 'foo']);
        $mockVehicles->shouldReceive('getHeaderTranslationKey')
            ->with($expectedData, 'vehicles')
            ->andReturn('review-vehicles-from-service');

        $this->sm->setService('Review\VariationVehicles', $mockVehicles);

        /** @var ApplicationCompletion $appCompletion */
        $appCompletion = m::mock(ApplicationCompletion::class)->makePartial();
        $appCompletion->setAddressesStatus(Application::VARIATION_STATUS_UNCHANGED);
        $appCompletion->setVehiclesStatus(Application::VARIATION_STATUS_UPDATED);
        $appCompletion->setPeopleStatus(Application::VARIATION_STATUS_UPDATED);

        $this->application->setIsVariation(true);
        $this->application->setNiFlag('Y');
        $this->application->setApplicationCompletion($appCompletion);
        $this->application->shouldReceive('isGoods')
            ->andReturn(true)
            ->shouldReceive('isSpecialRestricted')
            ->andReturn(false)
            ->shouldReceive('serialize')
            ->once()
            ->andReturn($expectedData);

        $this->niTranslation->shouldReceive('setLocaleForNiFlag')
            ->once()
            ->with('Y');

        $sections = [
            'vehicles' => 'bar',
            'community_licences' => 'test',
            'addresses' => 'foo',
        ];

        $this->sectionAccessService->shouldReceive('getAccessibleSections')
            ->with($this->application)
            ->andReturn($sections);

        $this->viewRenderer->shouldReceive('render')
            ->once()
            ->with(m::type(ViewModel::class))
            ->andReturnUsing(
                function (ViewModel $viewModel) {

                    $expected = [
                        'reviewTitle' => 'variation-review-title-gv',
                        'subTitle' => 'Foo ltd AB12345678/111',
                        'sections' => [
                            [
                                'header' => 'review-vehicles-from-service',
                                'config' => ['bar' => 'foo']
                            ]
                        ]
                    ];

                    $this->assertEquals('layout/review', $viewModel->getTemplate());
                    $this->assertTrue($viewModel->terminate());
                    $this->assertEquals($expected, $viewModel->getVariables());

                    return 'markup';
                }
            );

        $this->assertEquals('markup', $this->sut->generate($this->application, true));
    }

    public function testGenerateApplication()
    {
        $expectedData = [
            'sections' => [
                'vehicles',
                'people'
            ],
            'id' => 111,
            'isGoods' => true,
            'isSpecialRestricted' => false,
            'isInternal' => true,
            'licence' => [
                'organisation' => [
                    'name' => 'Foo ltd'
                ],
                'licNo' => 'AB12345678'
            ]
        ];

        $mockVehicles = m::mock();
        $mockVehicles->shouldReceive('getConfigFromData')
            ->with($expectedData)
            ->andReturn(['bar' => 'foo']);
        $mockVehicles->shouldReceive('getHeaderTranslationKey')
            ->with($expectedData, 'vehicles')
            ->andReturn('review-vehicles-from-service');

        $mockPeople = m::mock();
        $mockPeople->shouldReceive('getConfigFromData')
            ->with($expectedData)
            ->andReturn(['people' => 'foo']);
        $mockPeople->shouldReceive('getHeaderTranslationKey')
            ->with($expectedData, 'people')
            ->andReturn('review-people-from-service');

        $this->sm->setService('Review\ApplicationVehicles', $mockVehicles);
        $this->sm->setService('Review\ApplicationPeople', $mockPeople);

        $this->application->setIsVariation(false);
        $this->application->setNiFlag('Y');
        $this->application->shouldReceive('isGoods')
            ->andReturn(true)
            ->shouldReceive('isSpecialRestricted')
            ->andReturn(false)
            ->shouldReceive('serialize')
            ->once()
            ->andReturn($expectedData);

        $this->mockSignatureSection();

        $sections = [
            'vehicles' => 'bar',
            'community_licences' => 'test',
            'people' => 'foo',
        ];

        $this->niTranslation->shouldReceive('setLocaleForNiFlag')
            ->once()
            ->with('Y');

        $this->sectionAccessService->shouldReceive('getAccessibleSections')
            ->with($this->application)
            ->andReturn($sections);

        $this->viewRenderer->shouldReceive('render')
            ->once()
            ->with(m::type(ViewModel::class))
            ->andReturnUsing(
                function (ViewModel $viewModel) {

                    $expected = [
                        'reviewTitle' => 'application-review-title-gv',
                        'subTitle' => 'Foo ltd AB12345678/111',
                        'sections' => [
                            [
                                'header' => 'review-vehicles-from-service',
                                'config' => ['bar' => 'foo']
                            ],
                            [
                                'header' => 'review-people-from-service',
                                'config' => ['people' => 'foo']
                            ],
                            [
                                'hide-count' => true,
                                'config' => ['signature' => 'foo']
                            ]
                        ]
                    ];

                    $this->assertEquals('layout/review', $viewModel->getTemplate());
                    $this->assertTrue($viewModel->terminate());
                    $this->assertEquals($expected, $viewModel->getVariables());

                    return 'markup';
                }
            );

        $this->assertEquals('markup', $this->sut->generate($this->application, true));
    }

    public function testGenerateApplicationWithMappedSection()
    {
        $expectedData = [
            'sections' => [
                'vehicles',
                'undertakings'
            ],
            'id' => 111,
            'isGoods' => true,
            'isSpecialRestricted' => false,
            'isInternal' => true,
            'licence' => [
                'organisation' => [
                    'name' => 'Foo ltd'
                ],
                'licNo' => 'AB12345678'
            ]
        ];

        $mockVehicles = m::mock();
        $mockVehicles->shouldReceive('getConfigFromData')
            ->with($expectedData)
            ->andReturn(['bar' => 'foo']);
        $mockVehicles->shouldReceive('getHeaderTranslationKey')
            ->with($expectedData, 'vehicles')
            ->andReturn('review-vehicles-from-service');

        $mockUndertakings = m::mock();
        $mockUndertakings->shouldReceive('getConfigFromData')
            ->with($expectedData)
            ->andReturn(['undertakings' => 'foo']);
        $mockUndertakings->shouldReceive('getHeaderTranslationKey')
            ->with($expectedData, 'undertakings')
            ->andReturn('review-undertakings-from-service');

        $this->sm->setService('Review\ApplicationVehicles', $mockVehicles);
        $this->sm->setService('Review\ApplicationUndertakings', $mockUndertakings);

        $this->application->setIsVariation(false);
        $this->application->setNiFlag('Y');
        $this->application->shouldReceive('isGoods')
            ->andReturn(true)
            ->shouldReceive('isSpecialRestricted')
            ->andReturn(false)
            ->shouldReceive('serialize')
            ->once()
            ->andReturn($expectedData);


        $this->mockSignatureSection();

        $sections = [
            'vehicles' => 'bar',
            'community_licences' => 'test',
            'declarations_internal' => 'foo',
        ];

        $this->niTranslation->shouldReceive('setLocaleForNiFlag')
            ->once()
            ->with('Y');

        $this->sectionAccessService->shouldReceive('getAccessibleSections')
            ->with($this->application)
            ->andReturn($sections);

        $this->viewRenderer->shouldReceive('render')
            ->once()
            ->with(m::type(ViewModel::class))
            ->andReturnUsing(
                function (ViewModel $viewModel) {

                    $expected = [
                        'reviewTitle' => 'application-review-title-gv',
                        'subTitle' => 'Foo ltd AB12345678/111',
                        'sections' => [
                            [
                                'header' => 'review-vehicles-from-service',
                                'config' => ['bar' => 'foo']
                            ],
                            [
                                'header' => 'review-undertakings-from-service',
                                'config' => ['undertakings' => 'foo']
                            ],
                            [
                                'hide-count' => true,
                                'config' => ['signature' => 'foo']
                            ]
                        ]
                    ];

                    $this->assertEquals('layout/review', $viewModel->getTemplate());
                    $this->assertTrue($viewModel->terminate());
                    $this->assertEquals($expected, $viewModel->getVariables());

                    return 'markup';
                }
            );

        $this->assertEquals('markup', $this->sut->generate($this->application, true));
    }

    private function mockSignatureSection()
    {
        $signatureType = m::mock(RefData::class);
        $signatureType->shouldReceive('getId')
            ->andReturn(RefData::SIG_PHYSICAL_SIGNATURE);

        $organisation = m::mock(Organisation::class);
        $organisation->shouldReceive('getType->getId')->andReturn(Organisation::ORG_TYPE_LLP);

        $licence = m::mock(Licence::class);
        $licence->shouldReceive('getOrganisation')->andReturn($organisation);
        $licence->shouldReceive('isNi')->andReturn(true);

        $this->application->setSignatureType($signatureType);
        $this->application->setDigitalSignature(null);
        $this->application->setLicence($licence);

        $this->mockSignature->shouldReceive('getConfigFromData')
            ->with([
                'signatureType' => $signatureType,
                'digitalSignature' => null,
                'organisation'=> $organisation,
                'isNi' => true
            ])
            ->andReturn(['signature' => 'foo']);
    }
}
