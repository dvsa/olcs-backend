<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element;

interface ElementGeneratorInterface
{
    /**
     * Build and return an element instance using the appropriate data sources
     *
     * @param ElementGeneratorContext $context
     *
     * @return ElementInterface
     */
    public function generate(ElementGeneratorContext $context);
}
