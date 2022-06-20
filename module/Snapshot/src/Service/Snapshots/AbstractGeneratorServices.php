<?php

namespace Dvsa\Olcs\Snapshot\Service\Snapshots;

use Laminas\View\Renderer\RendererInterface;

/**
 * Abstract Generator Services
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class AbstractGeneratorServices
{
    /** @var RendererInterface */
    private $renderer;

    /**
     * Create service instance
     *
     * @param RendererInterface $viewRenderer
     *
     * @return AbstractGeneratorServices
     */
    public function __construct(RendererInterface $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * Return the renderer service
     *
     * @return RendererInterface
     */
    public function getRenderer(): RendererInterface
    {
        return $this->renderer;
    }
}
