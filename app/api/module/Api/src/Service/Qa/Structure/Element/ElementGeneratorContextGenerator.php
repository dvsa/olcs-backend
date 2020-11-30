<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element;

use Dvsa\Olcs\Api\Service\Qa\QaContext;
use Dvsa\Olcs\Api\Service\Qa\Structure\ValidatorListGenerator;

class ElementGeneratorContextGenerator
{
    /** @var ValidatorListGenerator */
    private $validatorListGenerator;

    /** @var ElementGeneratorContextFactory */
    private $elementGeneratorContextFactory;

    /**
     * Create service instance
     *
     * @param ValidatorListGenerator $validatorListGenerator
     * @param ElementGeneratorContextFactory $elementGeneratorContextFactory
     *
     * @return ApplicationStepGenerator
     */
    public function __construct(
        ValidatorListGenerator $validatorListGenerator,
        ElementGeneratorContextFactory $elementGeneratorContextFactory
    ) {
        $this->validatorListGenerator = $validatorListGenerator;
        $this->elementGeneratorContextFactory = $elementGeneratorContextFactory;
    }

    /**
     * Create and return an ElementGeneratorContext instance
     *
     * @param QaContext $qaContext
     *
     * @return ElementGeneratorContext
     */
    public function generate(QaContext $qaContext, $elementContainer)
    {
        $validatorList = $this->validatorListGenerator->generate(
            $qaContext->getApplicationStepEntity()
        );

        return $this->elementGeneratorContextFactory->create(
            $validatorList,
            $qaContext,
            $elementContainer
        );
    }
}
