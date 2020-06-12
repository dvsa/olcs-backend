<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Bilateral;

use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementGeneratorContext;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementGeneratorInterface;
use Dvsa\Olcs\Api\Service\Qa\Supports\AnyTrait;

class StandardAndCabotageGenerator implements ElementGeneratorInterface
{
    use AnyTrait;

    /** @var StandardAndCabotageFactory */
    private $standardAndCabotageFactory;

    /**
     * Create service instance
     *
     * @param StandardAndCabotageFactory $standardAndCabotageFactory
     *
     * @return StandardAndCabotageGenerator
     */
    public function __construct(StandardAndCabotageFactory $standardAndCabotageFactory)
    {
        $this->standardAndCabotageFactory = $standardAndCabotageFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(ElementGeneratorContext $context)
    {
        return $this->standardAndCabotageFactory->create(
            $context->getAnswerValue()
        );
    }
}
