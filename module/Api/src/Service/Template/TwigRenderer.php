<?php

namespace Dvsa\Olcs\Api\Service\Template;

use Twig\Environment;

class TwigRenderer
{
    /** @var Environment */
    private $twig;

    /**
     * Create service instance
     *
     * @param Environment $twig
     *
     * @return TwigRenderer
     */
    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * Render the template with the specified path using the supplied variables
     *
     * @param string $templatePath
     * @param array $variables
     *
     * @return string
     */
    public function render($templatePath, array $variables)
    {
        return $this->twig->render($templatePath, $variables);
    }

    /**
     * Render the template within the supplied string using the supplied variables
     *
     * @param string $templateString
     * @param array $variables
     *
     * @return string
     */
    public function renderString($templateString, array $variables)
    {
        $template = $this->twig->createTemplate($templateString);
        return $template->render($variables);
    }
}
