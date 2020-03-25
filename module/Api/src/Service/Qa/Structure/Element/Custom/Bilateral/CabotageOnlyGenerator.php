<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Bilateral;

use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementGeneratorContext;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementGeneratorInterface;
use Dvsa\Olcs\Api\Service\Qa\Supports\IrhpPermitApplicationOnlyTrait;

class CabotageOnlyGenerator implements ElementGeneratorInterface
{
    use IrhpPermitApplicationOnlyTrait;

    /** @var CabotageOnlyFactory */
    private $cabotageOnlyFactory;

    /**
     * Create service instance
     *
     * @param CabotageOnlyFactory $cabotageOnlyFactory
     *
     * @return CabotageOnlyGenerator
     */
    public function __construct(CabotageOnlyFactory $cabotageOnlyFactory)
    {
        $this->cabotageOnlyFactory = $cabotageOnlyFactory;
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

        $irhpPermitApplication = $context->getQaEntity();

        $countryName = $irhpPermitApplication->getIrhpPermitWindow()
            ->getIrhpPermitStock()
            ->getCountry()
            ->getCountryDesc();

        return $this->cabotageOnlyFactory->create($yesNo, $countryName);
    }
}
