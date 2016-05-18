<?php

namespace Dvsa\OlcsTest\Email\Service;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\Olcs\Email\Service\TemplateRenderer;
use Zend\View\Model\ViewModel;

/**
 * TemplateRendererTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class TemplateRendererTest extends MockeryTestCase
{
    /**
     * @dataProvider renderBodyProvider
     *
     * @param bool $hasHtml
     * @param int $htmlRenderTimes
     * @param string|null $renderedHtml
     */
    public function testRenderBody($hasHtml, $htmlRenderTimes, $renderedHtml)
    {
        $sut = new TemplateRenderer();
        $message = new \Dvsa\Olcs\Email\Data\Message('TO', 'SUBJECT');
        $message->setLocale('en_GB');
        $message->setHasHtml($hasHtml);

        $mockViewRenderer = m::mock(\Zend\View\Renderer\RendererInterface::class);
        $this->assertSame($sut, $sut->setViewRenderer($mockViewRenderer));

        $mockViewRenderer->shouldReceive('render')->once()->andReturnUsing(
            function (ViewModel $model) {
                $this->assertSame('en_GB/plain/TEMPLATE', $model->getTemplate());
                $this->assertSame(['var1', 'var2'], $model->getVariables()->getArrayCopy());

                return 'RENDER_PLAIN_TEMPLATE';
            }
        );

        $mockViewRenderer->shouldReceive('render')->once()->andReturnUsing(
            function (ViewModel $model) {
                $this->assertSame('en_GB/plain/default', $model->getTemplate());
                $this->assertSame(['content' => 'RENDER_PLAIN_TEMPLATE'], $model->getVariables()->getArrayCopy());

                return 'RENDER_PLAIN_LAYOUT';
            }
        );

        $mockViewRenderer->shouldReceive('render')->times($htmlRenderTimes)->andReturnUsing(
            function (ViewModel $model) {
                $this->assertSame('en_GB/html/TEMPLATE', $model->getTemplate());
                $this->assertSame(['var1', 'var2'], $model->getVariables()->getArrayCopy());

                return 'RENDER_HTML_TEMPLATE';
            }
        );

        $mockViewRenderer->shouldReceive('render')->times($htmlRenderTimes)->andReturnUsing(
            function (ViewModel $model) {
                $this->assertSame('en_GB/html/default', $model->getTemplate());
                $this->assertSame(['content' => 'RENDER_HTML_TEMPLATE'], $model->getVariables()->getArrayCopy());

                return 'RENDER_HTML_LAYOUT';
            }
        );

        $sut->renderBody($message, 'TEMPLATE', ['var1', 'var2']);

        $this->assertSame('RENDER_PLAIN_LAYOUT', $message->getPlainBody());
        $this->assertSame($renderedHtml, $message->getHtmlBody());
    }

    /**
     * Data provider for testRenderBody
     *
     * @return array
     */
    public function renderBodyProvider()
    {
        return [
            [true, 1, 'RENDER_HTML_LAYOUT'],
            [false, 0, null]
        ];
    }
}
