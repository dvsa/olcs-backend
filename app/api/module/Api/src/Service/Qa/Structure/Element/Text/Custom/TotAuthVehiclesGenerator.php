<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Text\Custom;

use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementGeneratorContext;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementGeneratorInterface;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Text\TextGenerator;

class TotAuthVehiclesGenerator implements ElementGeneratorInterface
{
    /** @var TextGenerator */
    private $textGenerator;

    /**
     * Create service instance
     *
     * @param TextGenerator $textGenerator
     *
     * @return TotAuthVehiclesGenerator
     */
    public function __construct(TextGenerator $textGenerator)
    {
        $this->textGenerator = $textGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(ElementGeneratorContext $context)
    {
        $textElement = $this->textGenerator->generate($context);

        $totAuthVehicles = $context->getIrhpApplicationEntity()->getLicence()->getTotAuthVehicles();

        $betweenRule = $context->getValidatorList()->getValidatorByRule('Between');
        $betweenRule->setParameter('min', 1);
        $betweenRule->setParameter('max', $totAuthVehicles);

        $label = $textElement->getHint();
        $label->setParameter(0, $totAuthVehicles);

        return $textElement;
    }
}