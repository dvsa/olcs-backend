<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText\Custom\EcmtShortTerm;

use Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText\QuestionTextGenerator;
use Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText\QuestionTextGeneratorInterface;
use Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText\QuestionTextGeneratorContext;

class PermitUsageGenerator implements QuestionTextGeneratorInterface
{
    /** @var QuestionTextGenerator */
    private $questionTextGenerator;

    /**
     * Create service instance
     *
     * @param QuestionTextGenerator $questionTextGenerator
     *
     * @return PermitUsageGenerator
     */
    public function __construct(QuestionTextGenerator $questionTextGenerator)
    {
        $this->questionTextGenerator = $questionTextGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(QuestionTextGeneratorContext $context)
    {
        $questionText = $this->questionTextGenerator->generate($context);

        $validityYear = $context->getIrhpApplicationEntity()
            ->getFirstIrhpPermitApplication()
            ->getIrhpPermitWindow()
            ->getIrhpPermitStock()
            ->getValidityYear();

        if ($validityYear == 2019) {
            $guidanceTranslateableText = $questionText->getAdditionalGuidance()->getTranslateableText();
            $guidanceTranslateableText->setKey('qanda.ecmt-short-term.permit-usage.additional-guidance.2019');
        }

        return $questionText;
    }
}
