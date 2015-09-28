<?php

/**
 * Generator Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Snapshot\Service\Snapshots\TransportManagerApplication;

use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Api\Entity\Tm\TransportManagerApplication;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\Olcs\Snapshot\Service\Snapshots\TransportManagerApplication\Generator;
use OlcsTest\Bootstrap;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Model\ViewModel;

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

    protected $services;

    public function setUp()
    {
        $sm = m::mock(ServiceLocatorInterface::class);

        $this->services = [
            'Review\TransportManagerMain' => m::mock(),
            'Review\TransportManagerResponsibility' => m::mock(),
            'Review\TransportManagerOtherEmployment' => m::mock(),
            'Review\TransportManagerPreviousConviction' => m::mock(),
            'Review\TransportManagerPreviousLicence' => m::mock(),
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

    public function testGenerate()
    {
        $organisation = m::mock(Organisation::class);
        $organisation->shouldReceive('getName')->andReturn('Foo ltd');

        $licence = m::mock(Licence::class);
        $licence->shouldReceive('getOrganisation')->andReturn($organisation);
        $licence->shouldReceive('getLicNo')->andReturn('AB1234567');

        $application = m::mock(Application::class);
        $application->shouldReceive('getLicence')->andReturn($licence);
        $application->shouldReceive('getId')->andReturn(111);

        /** @var TransportManagerApplication $tma */
        $tma = m::mock(TransportManagerApplication::class);
        $tma->shouldReceive('getApplication')->andReturn($application);

        $this->services['Review\TransportManagerMain']
            ->shouldReceive('getConfig')->once()->with($tma)->andReturn('tmMain');

        $this->services['Review\TransportManagerResponsibility']
            ->shouldReceive('getConfig')->once()->with($tma)->andReturn('tmResponsibility');

        $this->services['Review\TransportManagerOtherEmployment']
            ->shouldReceive('getConfig')->once()->with($tma)->andReturn('tmEmployment');

        $this->services['Review\TransportManagerPreviousConviction']
            ->shouldReceive('getConfig')->once()->with($tma)->andReturn('tmConviction');

        $this->services['Review\TransportManagerPreviousLicence']
            ->shouldReceive('getConfig')->once()->with($tma)->andReturn('tmPreviousLicence');

        $this->services['ViewRenderer']->shouldReceive('render')
            ->once()
            ->with(m::type(ViewModel::class))
            ->andReturnUsing(
                function ($view) {
                    return $view;
                }
            );

        /** @var ViewModel $result */
        $result = $this->sut->generate($tma);

        $this->assertInstanceOf(ViewModel::class, $result);
        $this->assertEquals('layout/review', $result->getTemplate());

        $params = $result->getVariables();

        $expected = [
            'reviewTitle' => 'tm-review-title',
            'subTitle' => 'Foo ltd AB1234567/111',
            'settings' => [
                'hide-count' => true
            ],
            'sections' => [
                [
                    'header' => 'tm-review-main',
                    'config' => 'tmMain'
                ],
                [
                    'header' => 'tm-review-responsibility',
                    'config' => 'tmResponsibility'
                ],
                [
                    'header' => 'tm-review-other-employment',
                    'config' => 'tmEmployment'
                ],
                [
                    'header' => 'tm-review-previous-conviction',
                    'config' => 'tmConviction'
                ],
                [
                    'header' => 'tm-review-previous-licence',
                    'config' => 'tmPreviousLicence'
                ]
            ]
        ];

        $this->assertEquals($expected, $params);
    }
}
