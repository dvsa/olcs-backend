<?php

namespace Dvsa\OlcsTest\Email\Service;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\Olcs\Email\Service\TemplateRenderer;

/**
 * TemplateRendererTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class TemplateRendererTest extends MockeryTestCase
{
    protected $sut;

    public function setUp()
    {
        $this->sut = new TemplateRenderer();
    }

    public function testRenderBody()
    {
        $this->assertNull($this->sut->getDefaultLayout());
        $this->assertSame($this->sut, $this->sut->setDefaultLayout('foobar'));
        $this->assertSame('foobar', $this->sut->getDefaultLayout());#

        $message = new \Dvsa\Olcs\Email\Data\Message('TO', 'SUBJECT');
        $message->setLocale('LC');

        $mockViewRenderer = m::mock(\Zend\View\Renderer\RendererInterface::class);
        $this->assertSame($this->sut, $this->sut->setViewRenderer($mockViewRenderer));
        $this->assertSame($this->sut, $this->sut->setDefaultLayout('LAYOUT'));

        $mockViewRenderer->shouldReceive('render')->once()->andReturnUsing(
            function (\Zend\View\Model\ViewModel $model) {
                $this->assertSame('LC/TEMPLATE', $model->getTemplate());
                $this->assertSame(['var1', 'var2'], $model->getVariables()->getArrayCopy());

                return 'RENDER_TEMPLATE';
            }
        );

        $mockViewRenderer->shouldReceive('render')->once()->andReturnUsing(
            function (\Zend\View\Model\ViewModel $model) {
                $this->assertSame('LAYOUT', $model->getTemplate());
                $this->assertSame(['content' => 'RENDER_TEMPLATE'], $model->getVariables()->getArrayCopy());

                return 'RENDER_LAYOUT';
            }
        );

        $this->sut->renderBody($message, 'TEMPLATE', ['var1', 'var2']);

        $this->assertSame('RENDER_LAYOUT', $message->getBody());
    }

    public function testRenderBodyNoLayout()
    {

        $message = new \Dvsa\Olcs\Email\Data\Message('TO', 'SUBJECT');
        $message->setLocale('LC');

        $mockViewRenderer = m::mock(\Zend\View\Renderer\RendererInterface::class);
        $this->assertSame($this->sut, $this->sut->setViewRenderer($mockViewRenderer));

        $mockViewRenderer->shouldReceive('render')->once()->andReturnUsing(
            function (\Zend\View\Model\ViewModel $model) {
                $this->assertSame('LC/TEMPLATE', $model->getTemplate());
                $this->assertSame(['var1', 'var2'], $model->getVariables()->getArrayCopy());

                return 'RENDER_TEMPLATE';
            }
        );

        $this->sut->renderBody($message, 'TEMPLATE', ['var1', 'var2'], false);

        $this->assertSame('RENDER_TEMPLATE', $message->getBody());
    }
}
