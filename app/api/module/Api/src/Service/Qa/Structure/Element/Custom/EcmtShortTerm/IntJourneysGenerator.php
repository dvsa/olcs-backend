<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\EcmtShortTerm;

use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementGeneratorContext;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementGeneratorInterface;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Radio\RadioGenerator;

class IntJourneysGenerator implements ElementGeneratorInterface
{
    /** @var IntJourneysFactory */
    private $intJourneysFactory;

    /** @var RadioGenerator */
    private $radioGenerator;

    /**
     * Create service instance
     *
     * @param IntJourneysFactory $intJourneysFactory
     * @param RadioGenerator $radioGenerator
     *
     * @return IntJourneysGenerator
     */
    public function __construct(IntJourneysFactory $intJourneysFactory, RadioGenerator $radioGenerator)
    {
        $this->intJourneysFactory = $intJourneysFactory;
        $this->radioGenerator = $radioGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(ElementGeneratorContext $context)
    {
        $irhpApplication = $context->getIrhpApplicationEntity();

        $isNi = $irhpApplication->getLicence()->isNi();
        $radio = $this->radioGenerator->generate($context);

        return $this->intJourneysFactory->create($isNi, $radio);
    }
}
