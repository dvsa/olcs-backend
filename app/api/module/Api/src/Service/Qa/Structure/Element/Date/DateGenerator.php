<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Date;

use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementGeneratorContext;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementGeneratorInterface;

class DateGenerator implements ElementGeneratorInterface
{
    /** @var DateFactory */
    private $dateFactory;

    /**
     * Create service instance
     *
     * @param DateFactory $dateFactory
     *
     * @return Date
     */
    public function __construct(DateFactory $dateFactory)
    {
        $this->dateFactory = $dateFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(ElementGeneratorContext $context)
    {
        $applicationStepEntity = $context->getApplicationStepEntity();
        $irhpApplicationEntity = $context->getIrhpApplicationEntity();

        return $this->dateFactory->create(
            $irhpApplicationEntity->getAnswer($applicationStepEntity)
        );
    }
}
