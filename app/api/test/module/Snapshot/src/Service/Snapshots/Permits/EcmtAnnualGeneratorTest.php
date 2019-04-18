<?php

namespace Dvsa\OlcsTest\Snapshot\Service\Snapshots\Permits;

use Dvsa\Olcs\Snapshot\Service\Snapshots\Permits\EcmtAnnualGenerator;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use OlcsTest\Bootstrap;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\View\Model\ViewModel;
use Zend\View\Renderer\PhpRenderer;

class EcmtAnnualGeneratorTest extends MockeryTestCase
{
    /**
     * @var EcmtAnnualGenerator
     */
    protected $sut;

    /**
     * @var PhpRenderer
     */
    protected $viewRenderer;

    protected function setUp()
    {
        $this->sut = new EcmtAnnualGenerator();
        $this->viewRenderer = m::mock(PhpRenderer::class);

        $sm = Bootstrap::getServiceManager();
        $sm->setService('ViewRenderer', $this->viewRenderer);
        $this->sut->setServiceLocator($sm);
    }

    public function testGenerateWithNoPermitApplication()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Snapshot generator expects ECMT permit application record');

        $this->sut->setData(['entity' => null]);
        $this->sut->generate();
    }

    public function testGenerate()
    {
        $operatorName = 'operator name';
        $permitType = 'permit type';
        $applicationRef = 'ref';
        $questionAnswerData = ['data'];
        $html = '<html>';

        $ecmtPermitApplication = m::mock(EcmtPermitApplication::class);

        $ecmtPermitApplication
            ->shouldReceive('getLicence->getOrganisation->getName')
            ->once()
            ->withNoArgs()
            ->andReturn($operatorName);

        $ecmtPermitApplication
            ->shouldReceive('getPermitType->getDescription')
            ->once()
            ->withNoArgs()
            ->andReturn($permitType);

        $ecmtPermitApplication
            ->shouldReceive('getApplicationRef')
            ->once()
            ->withNoArgs()
            ->andReturn($applicationRef);

        $ecmtPermitApplication
            ->shouldReceive('getQuestionAnswerData')
            ->once()
            ->withNoArgs()
            ->andReturn($questionAnswerData);

        $config = [
            'permitType' => $permitType,
            'operator' => $operatorName,
            'ref' => $applicationRef,
            'questionAnswerData' => $questionAnswerData,
            'guidanceDeclaration' => [
                'bullets' => [
                    'permits.ecmt.declaration.bullet.guidance.note',
                    'permits.ecmt.declaration.bullet.guidance.restricted.countries',
                    'permits.ecmt.declaration.bullet.guidance.issued.logbook',
                    'permits.ecmt.declaration.bullet.guidance.carry.logbook',
                ],
                'declaration' => 'permits.ecmt.declaration',
            ],
        ];

        $template = 'layout/permit-application';

        $this->viewRenderer->shouldReceive('render')
            ->once()
            ->with(m::type(ViewModel::class))
            ->andReturnUsing(
                function (ViewModel $viewModel) use ($config, $template, $html) {
                    $this->assertEquals($config, $viewModel->getVariables());
                    $this->assertEquals($template, $viewModel->getTemplate());
                    $this->assertTrue($viewModel->terminate());

                    return $html;
                }
            );

        $this->sut->setData(['entity' => $ecmtPermitApplication]);
        $this->sut->generate();
    }
}
