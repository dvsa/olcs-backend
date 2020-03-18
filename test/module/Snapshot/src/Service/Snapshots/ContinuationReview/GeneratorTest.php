<?php

namespace Dvsa\OlcsTest\Snapshot\Service\Snapshots\ContinuationReview;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\Olcs\Snapshot\Service\Snapshots\ContinuationReview\Generator;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Model\ViewModel;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Licence\ContinuationDetail;
use Dvsa\Olcs\Snapshot\Service\Snapshots\ContinuationReview\Section\OperatingCentresReviewService;

/**
 * Generator Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class GeneratorTest extends MockeryTestCase
{
    /**
     * @var Generator
     */
    protected $sut;

    protected $services;

    public function setUp()
    {
        $sm = m::mock(ServiceLocatorInterface::class);

        $this->services = [
            'Utils\NiTextTranslation' => m::mock(),
            'SectionAccessService' => m::mock(),
            'ContinuationReview\TypeOfLicence' => m::mock(),
            'ContinuationReview\OperatingCentres' => m::mock(OperatingCentresReviewService::class)->makePartial(),
            'ViewRenderer' => m::mock()
        ];

        $sm->shouldReceive('get')->andReturnUsing(
            function ($key) {
                return $this->services[$key];
            }
        );
        $sm->shouldReceive('has')->andReturnUsing(
            function ($key) {
                return array_key_exists($key, $this->services);
            }
        );

        $this->sut = new Generator();
        $this->sut->setServiceLocator($sm);
    }

    public function testGenerate()
    {
        $sections = [
            'type_of_licence' => 'foo' ,
            'operating_centres' => 'foo' ,
            Generator::PEOPLE_SECTION => 'foo',
            Generator::TRAILERS_SECTION => 'foo',
            Generator::TAXI_PHV_SECTION => 'foo',
            Generator::DISCS_SECTION => 'foo',
            Generator::COMMUNITY_LICENCES_SECTION => 'foo',
            Generator::CONDITIONS_UNDERTAKINGS_SECTION => 'foo'
        ];

        $mockLicence = m::mock(Licence::class)
            ->shouldReceive('getNiFlag')
            ->andReturn('N')
            ->once()
            ->shouldReceive('getLicenceType')
            ->andReturn(
                m::mock()
                    ->shouldReceive('getId')
                    ->andReturn(Licence::LICENCE_TYPE_STANDARD_NATIONAL)
                    ->once()
                    ->getMock()
            )
            ->once()
            ->shouldReceive('getConditionUndertakings')
            ->andReturn([])
            ->once()
            ->shouldReceive('getOrganisation')
            ->andReturn(
                m::mock()
                    ->shouldReceive('getType')
                    ->andReturn(
                        m::mock()
                            ->shouldReceive('getId')
                            ->andReturn('org_typ_rc')
                            ->once()
                            ->getMock()
                    )
                    ->once()
                    ->shouldReceive('getName')
                    ->andReturn('BAR LTD')
                    ->once()
                    ->getMock()
            )
            ->twice()
            ->shouldReceive('getLicNo')
            ->andReturn('OB123')
            ->once()
            ->getMock();

        $continuationDetail = m::mock(ContinuationDetail::class)
            ->shouldReceive('getLicence')
            ->andReturn($mockLicence)
            ->times(4)
            ->getMock();

        $this->services['Utils\NiTextTranslation']
            ->shouldReceive('setLocaleForNiFlag')
            ->with('N')
            ->once();

        $this->services['SectionAccessService']
            ->shouldReceive('getAccessibleSectionsForLicenceContinuation')
            ->with($mockLicence)
            ->andReturn($sections)
            ->once();

        $this->services['ContinuationReview\TypeOfLicence']
            ->shouldReceive('getConfigFromData')
            ->with($continuationDetail)
            ->once()
            ->andReturn('type-of-licence');

        $this->services['ContinuationReview\OperatingCentres']
            ->shouldReceive('getConfigFromData')
            ->with($continuationDetail)
            ->andReturn('operating-centres')
            ->once()
            ->shouldReceive('getSummaryFromData')
            ->with($continuationDetail)
            ->andReturn('operating-centres-summary')
            ->once()
            ->shouldReceive('getSummaryHeader')
            ->with($continuationDetail)
            ->andReturn('operating-centres-summary-header')
            ->once();

        $this->services['ViewRenderer']->shouldReceive('render')
            ->once()
            ->with(m::type(ViewModel::class))
            ->andReturnUsing(
                function ($view) {
                    return $view;
                }
            );

        /** @var ViewModel $result */
        $result = $this->sut->generate($continuationDetail);

        $this->assertInstanceOf(ViewModel::class, $result);
        $this->assertEquals('layout/continuation-review', $result->getTemplate());

        $params = $result->getVariables();

        $expected = [
            'reviewTitle' => 'BAR LTD OB123',
            'subTitle' => 'continuation-review-subtitle',
            'sections' => [
                [
                    'header' => 'continuation-review-type_of_licence',
                    'config' => 'type-of-licence'
                ],
                [
                    'header' => 'continuation-review-operating_centres',
                    'config' => 'operating-centres',
                    'summary' => 'operating-centres-summary',
                    'summaryHeader' => 'operating-centres-summary-header',
                ],
                [
                    'header' => 'continuation-review-people-org_typ_rc',
                    'config' => ''
                ],
                [
                    'header' => 'continuation-review-finance',
                    'config' => ''
                ],
                [
                    'header' => 'continuation-review-declaration',
                    'config' => ''
                ],
            ]
        ];

        $this->assertEquals($expected, $params);
    }
}
