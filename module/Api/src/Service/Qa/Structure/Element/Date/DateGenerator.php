<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Date;

use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementGeneratorContext;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementGeneratorInterface;
use Dvsa\Olcs\Api\Service\Qa\Supports\IrhpApplicationOnlyTrait;

class DateGenerator implements ElementGeneratorInterface
{
    use IrhpApplicationOnlyTrait;

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
        return $this->dateFactory->create(
            $context->getAnswerValue()
        );
    }
}
