<?php

namespace Dvsa\OlcsTest\Snapshot\Service\Snapshots\Permits;

use Dvsa\Olcs\Snapshot\Service\Snapshots\Permits\IrhpGenerator;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use OlcsTest\Bootstrap;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\View\Model\ViewModel;
use Zend\View\Renderer\PhpRenderer;

/**
 * Class IrhpGeneratorTest
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class IrhpGeneratorTest extends MockeryTestCase
{
    /**
     * @var IrhpGenerator
     */
    protected $sut;

    /**
     * @var PhpRenderer
     */
    protected $viewRenderer;

    protected function setUp()
    {
        $this->sut = new IrhpGenerator();
        $this->viewRenderer = m::mock(PhpRenderer::class);

        $sm = Bootstrap::getServiceManager();
        $sm->setService('ViewRenderer', $this->viewRenderer);
        $this->sut->setServiceLocator($sm);
    }

    public function testGenerateWithNoPermitApplication()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Snapshot generator expects IRHP application record');

        $this->sut->setData(['entity' => null]);
        $this->sut->generate();
    }

    public function testGenerate()
    {
        $operatorName = 'operator name';
        $permitType = 'permit type';
        $applicationRef = 'ref';
        $questionAnswerData = [
            'slug' => 'data1',
            'slug2' => 'data2',
        ];

        $html = '<html>';

        $irhpApplication = m::mock(IrhpApplication::class);

        $irhpApplication
            ->shouldReceive('getLicence->getOrganisation->getName')
            ->once()
            ->withNoArgs()
            ->andReturn($operatorName);

        $irhpApplication
            ->shouldReceive('getIrhpPermitType->getName->getDescription')
            ->once()
            ->withNoArgs()
            ->andReturn($permitType);

        $irhpApplication
            ->shouldReceive('getApplicationRef')
            ->once()
            ->withNoArgs()
            ->andReturn($applicationRef);

        $irhpApplication
            ->shouldReceive('getQuestionAnswerData')
            ->once()
            ->with(true)
            ->andReturn($questionAnswerData);

        $config = [
            'permitType' => $permitType,
            'operator' => $operatorName,
            'ref' => $applicationRef,
            'questionAnswerData' => $questionAnswerData,
            'guidanceDeclaration' => [
                'bullets' => [
                    'permits.irhp.declaration.bullet.guidance.note',
                    'permits.irhp.declaration.bullet.conditions',
                    'permits.irhp.declaration.bullet.guidance.carry',
                    'permits.irhp.declaration.bullet.guidance.transport',
                ],
                'declaration' => 'permits.snapshot.declaration',
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

        $this->sut->setData(['entity' => $irhpApplication]);
        $this->sut->generate();
    }
}
