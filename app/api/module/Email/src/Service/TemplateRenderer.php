<?php

namespace Dvsa\Olcs\Email\Service;

use Zend\View\Model\ViewModel;
use Dvsa\Olcs\Email\Data\Message;

/**
 * Class Client
 */
class TemplateRenderer
{
    /**
     * \Zend\View\Renderer\RendererInterface
     */
    protected $viewRenderer;

    /**
     * string
     */
    protected $defaultLayout;

    /**
     * @return \Zend\View\Renderer\RendererInterface
     */
    public function getViewRenderer()
    {
        return $this->viewRenderer;
    }

    /**
     * @param \Zend\View\Renderer\RendererInterface $viewRenderer
     *
     * @return \Dvsa\Olcs\Email\Service\TemplateRenderer
     */
    public function setViewRenderer(\Zend\View\Renderer\RendererInterface $viewRenderer)
    {
        $this->viewRenderer = $viewRenderer;
        return $this;
    }

    /**
     * @param string $defaultLayout
     *
     * @return \Dvsa\Olcs\Email\Service\TemplateRenderer
     */
    public function setDefaultLayout($defaultLayout)
    {
        $this->defaultLayout = $defaultLayout;
        return $this;
    }

    /**
     * @return string
     */
    public function getDefaultLayout()
    {
        return $this->defaultLayout;
    }

    /**
     * Render a template into the message body
     *
     * @param Message $message
     * @param string|array $templates
     * @param array $variables
     * @param string|bool $layout
     */
    public function renderBody(Message $message, $templates, $variables = [], $layout = null)
    {
        $content = $this->getEmailContent($message->getLocale(), $templates, $variables);

        if ($layout !== false) {
            if ($layout === null) {
                $layout = $this->getDefaultLayout();
            }
            $layoutView = new ViewModel();
            $layoutView->setTemplate($layout);
            $layoutView->setVariable('content', $content);
            $message->setBody($this->getViewRenderer()->render($layoutView));
        } else {
            $message->setBody($content);
        }
    }

    /**
     * @param string $locale
     * @param string|array $templates
     * @param array $variables
     * @return string
     */
    private function getEmailContent($locale, $templates, array $variables = [])
    {
        $templateViews = [];

        if (!is_array($templates)) {
            $templates = [$templates];
        }

        foreach ($templates as $template) {
            $templateViews[] = $this->getTemplateView($locale, $template, $variables);
        }

        return implode($templateViews);
    }

    /**
     * @param string $locale
     * @param string $template
     * @param array $variables
     * @return string
     */
    private function getTemplateView($locale, $template, array $variables = [])
    {
        $templateView = new ViewModel();
        $templateView->setTemplate($locale .'/'. $template);
        $templateView->setVariables($variables);

        return $this->getViewRenderer()->render($templateView);
    }
}
