<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Bilateral;

use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementGeneratorContext;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementGeneratorInterface;
use Dvsa\Olcs\Api\Service\Qa\Supports\IrhpPermitApplicationOnlyTrait;

class NoOfPermitsMoroccoGenerator implements ElementGeneratorInterface
{
    use IrhpPermitApplicationOnlyTrait;

    /** @var NoOfPermitsMoroccoFactory */
    private $noOfPermitsMoroccoFactory;

    /**
     * Create service instance
     *
     * @param NoOfPermitsMoroccoFactory $noOfPermitsMoroccoFactory
     *
     * @return NoOfPermitsMoroccoGenerator
     */
    public function __construct(NoOfPermitsMoroccoFactory $noOfPermitsMoroccoFactory)
    {
        $this->noOfPermitsMoroccoFactory = $noOfPermitsMoroccoFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(ElementGeneratorContext $context)
    {
        $irhpPermitApplication = $context->getQaEntity();

        $label = $irhpPermitApplication->getIrhpPermitWindow()
            ->getIrhpPermitStock()
            ->getPeriodNameKey();

        $bilateralRequired = $irhpPermitApplication->getFilteredBilateralRequired();
        $value = $bilateralRequired[IrhpPermitApplication::BILATERAL_MOROCCO_REQUIRED];

        return $this->noOfPermitsMoroccoFactory->create($label, $value);
    }
}
