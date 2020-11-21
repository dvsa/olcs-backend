<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\CertRoadworthiness;

use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Common\DateWithThresholdGenerator;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementGeneratorContext;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementGeneratorInterface;
use Dvsa\Olcs\Api\Service\Qa\Supports\IrhpApplicationOnlyTrait;

class MotExpiryDateGenerator implements ElementGeneratorInterface
{
    use IrhpApplicationOnlyTrait;

    const DATE_THRESHOLD = 'P14M';

    /** @var MotExpiryDateFactory */
    private $motExpiryDateFactory;

    /** @var DateWithThresholdGenerator */
    private $dateWithThresholdGenerator;

    /**
     * Create service instance
     *
     * @param MotExpiryDateFactory $motExpiryDateFactory
     * @param DateWithThresholdGenerator $dateWithThresholdGenerator
     *
     * @return MotExpiryDateGenerator
     */
    public function __construct(
        MotExpiryDateFactory $motExpiryDateFactory,
        DateWithThresholdGenerator $dateWithThresholdGenerator
    ) {
        $this->motExpiryDateFactory = $motExpiryDateFactory;
        $this->dateWithThresholdGenerator = $dateWithThresholdGenerator;
    }

    /**
     * Build and return an element instance using the appropriate data sources
     *
     * @param ElementGeneratorContext $context
     *
     * @return ElementInterface
     */
    public function generate(ElementGeneratorContext $context)
    {
        $irhpApplication = $context->getQaEntity();
        $enableFileUploads = $context->isSelfservePageContainer() && $irhpApplication->getLicence()->isNi();

        return $this->motExpiryDateFactory->create(
            $enableFileUploads,
            $this->dateWithThresholdGenerator->generate($context, self::DATE_THRESHOLD)
        );
    }
}
