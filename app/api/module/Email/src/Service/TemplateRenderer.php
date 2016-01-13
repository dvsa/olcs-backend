<?php

namespace Dvsa\Olcs\Email\Service;

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
     * @param \Dvsa\Olcs\Email\Data\Message $message
     * @param string $template
     * @param array $variables
     * @param string|bool $layout
     */
    public function renderBody(\Dvsa\Olcs\Email\Data\Message $message, $template, $variables = [], $layout = null)
    {
        $templateView = new \Zend\View\Model\ViewModel();
        $templateView->setTemplate($message->getLocale() .'/'. $template);
        $templateView->setVariables($variables);

        if ($layout !== false) {
            if ($layout === null) {
                $layout = $this->getDefaultLayout();
            }
            $layoutView = new \Zend\View\Model\ViewModel();
            $layoutView->setTemplate($layout);
            $layoutView->setVariable('content', $this->getViewRenderer()->render($templateView));
            $message->setBody($this->getViewRenderer()->render($layoutView));
        } else {
            $message->setBody($this->getViewRenderer()->render($templateView));
        }
    }
}
