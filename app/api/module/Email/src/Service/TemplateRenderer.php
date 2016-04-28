<?php

namespace Dvsa\Olcs\Email\Service;

use Zend\View\Model\ViewModel;
use Dvsa\Olcs\Email\Data\Message;
use Zend\View\Renderer\RendererInterface;

/**
 * Class Client
 */
class TemplateRenderer
{
    /**
     * RendererInterface
     */
    protected $viewRenderer;

    /**
     * @return RendererInterface
     */
    public function getViewRenderer()
    {
        return $this->viewRenderer;
    }

    /**
     * @param RendererInterface $viewRenderer
     *
     * @return \Dvsa\Olcs\Email\Service\TemplateRenderer
     */
    public function setViewRenderer(RendererInterface $viewRenderer)
    {
        $this->viewRenderer = $viewRenderer;
        return $this;
    }

    /**
     * Render a template into the message body
     *
     * @param Message $message
     * @param string|array $templates
     * @param array $variables
     * @param string|bool $layout
     */
    public function renderBody(Message $message, $templates, $variables = [], $layout = 'default')
    {
        $locale = $message->getLocale();

        $plainContent = $this->getEmailContent($locale, $templates, 'plain', $variables);
        $message->setPlainBody($this->getLayoutView($locale, $layout, 'plain', $plainContent));

        //works around inspection request email which doesn't have a HTML version, and sets the HTML variable to false
        if ($message->getHasHtml()) {
            $htmlContent = $this->getEmailContent($locale, $templates, 'html', $variables);
            $message->setHtmlBody($this->getLayoutView($locale, $layout, 'html', $htmlContent));
        }
    }

    /**
     * @param string $locale
     * @param string $layout
     * @param string $format
     * @param string $content
     *
     * @return string
     */
    private function getLayoutView($locale, $layout, $format, $content)
    {
        $layoutView = new ViewModel();
        $layoutView->setTemplate($locale .'/'. $format .'/'. $layout);
        $layoutView->setVariable('content', $content);

        return $this->getViewRenderer()->render($layoutView);
    }

    /**
     * @param string $locale
     * @param string|array $templates
     * @param string $format
     * @param array $variables
     * @return string
     */
    private function getEmailContent($locale, $templates, $format, array $variables = [])
    {
        $templateViews = [];

        if (!is_array($templates)) {
            $templates = [$templates];
        }

        foreach ($templates as $template) {
            $templateViews[] = $this->getTemplateView($locale, $template, $format, $variables);
        }

        return implode($templateViews);
    }

    /**
     * @param string $locale
     * @param string $template
     * @param string $format
     * @param array $variables
     * @return string
     */
    private function getTemplateView($locale, $template, $format, array $variables = [])
    {
        $templateView = new ViewModel();
        $templateView->setTemplate($locale .'/'. $format .'/'. $template);
        $templateView->setVariables($variables);

        return $this->getViewRenderer()->render($templateView);
    }
}
