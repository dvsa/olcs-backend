<?php

namespace Dvsa\Olcs\Api\Service\Template;

use Twig\Loader\LoaderInterface as TwigLoader;
use Laminas\View\Model\ViewModel;
use Laminas\View\Renderer\RendererInterface;

class StrategySelectingViewRenderer
{
    /** @var RendererInterface */
    private $legacyViewRenderer;

    /** @var TwigRenderer */
    private $twigRenderer;

    /** @var LoaderInterface */
    private $twigLoader;

    /**
     * Create service instance
     *
     * @param RendererInterface $legacyViewRenderer
     * @param TwigRenderer $twigRenderer
     * @param TwigLoader $twigLoader
     *
     * @return StrategySelectingViewRenderer
     */
    public function __construct(
        RendererInterface $legacyViewRenderer,
        TwigRenderer $twigRenderer,
        TwigLoader $twigLoader
    ) {
        $this->legacyViewRenderer = $legacyViewRenderer;
        $this->twigRenderer = $twigRenderer;
        $this->twigLoader = $twigLoader;
    }

    /**
     * Render the view using the editable twig template if available, otherwise fall back on the legacy template
     * within the filesystem
     *
     * @param string $locale
     * @param string $format
     * @param string $template
     * @param array $variables
     *
     * @return string
     */
    public function render($locale, $format, $template, array $variables)
    {
        $templatePath = implode('/', [$locale, $format, $template]);

        if ($this->twigLoader->exists($templatePath)) {
            return $this->twigRenderer->render($templatePath, $variables);
        }

        $templateView = new ViewModel();
        $templateView->setTemplate($templatePath);
        $templateView->setVariables($variables);

        return $this->legacyViewRenderer->render($templateView);
    }
}
