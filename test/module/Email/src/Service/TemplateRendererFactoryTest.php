<?php

namespace Dvsa\OlcsTest\Email\Service;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\Olcs\Api\Service\Template\StrategySelectingViewRenderer;
use Dvsa\Olcs\Email\Service\TemplateRendererFactory;

/**
 * TemplateRendererFactoryTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class TemplateRendererFactoryTest extends MockeryTestCase
{
    protected $sut;

    public function setUp()
    {
        $this->sut = new TemplateRendererFactory();
    }

    public function testCreateService()
    {
        $mockViewRenderer = m::mock(StrategySelectingViewRenderer::class);
        $sl = m::mock(\Zend\ServiceManager\ServiceLocatorInterface::class);
        $sl->shouldReceive('get')->with('TemplateStrategySelectingViewRenderer')->once()->andReturn($mockViewRenderer);
        $service = $this->sut->createService($sl);

        $this->assertSame($mockViewRenderer, $service->getViewRenderer());
    }
}
