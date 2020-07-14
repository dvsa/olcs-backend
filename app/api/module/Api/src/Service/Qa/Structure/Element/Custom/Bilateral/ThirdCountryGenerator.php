<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Bilateral;

use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementGeneratorContext;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementGeneratorInterface;
use Dvsa\Olcs\Api\Service\Qa\Supports\IrhpPermitApplicationOnlyTrait;

class ThirdCountryGenerator implements ElementGeneratorInterface
{
    use IrhpPermitApplicationOnlyTrait;

    /** @var ThirdCountryFactory */
    private $thirdCountryFactory;

    /**
     * Create service instance
     *
     * @param ThirdCountryFactory $thirdCountryFactory
     *
     * @return ThirdCountryGenerator
     */
    public function __construct(ThirdCountryFactory $thirdCountryFactory)
    {
        $this->thirdCountryFactory = $thirdCountryFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(ElementGeneratorContext $context)
    {
        $yesNo = null;
        if ($context->getAnswerValue()) {
            $yesNo = 'Y';
        }

        return $this->thirdCountryFactory->create($yesNo);
    }
}
