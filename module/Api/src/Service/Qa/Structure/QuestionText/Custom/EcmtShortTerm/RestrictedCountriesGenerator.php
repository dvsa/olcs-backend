<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText\Custom\EcmtShortTerm;

use Dvsa\Olcs\Api\Entity\Generic\ApplicationPathGroup;
use Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText\QuestionTextGenerator;
use Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText\QuestionTextGeneratorInterface;
use Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText\QuestionTextGeneratorContext;

class RestrictedCountriesGenerator implements QuestionTextGeneratorInterface
{
    /** @var QuestionTextGenerator */
    private $questionTextGenerator;

    /**
     * Create service instance
     *
     * @param QuestionTextGenerator $questionTextGenerator
     *
     * @return RestrictedCountriesGenerator
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
        $irhpApplicationEntity = $context->getIrhpApplicationEntity();
        $questionText = $this->questionTextGenerator->generate($context);

        $applicationPathGroupId = $irhpApplicationEntity->getAssociatedStock()->getApplicationPathGroup()->getId();
        if ($applicationPathGroupId == ApplicationPathGroup::ECMT_SHORT_TERM_2020_APSG_WITHOUT_SECTORS_ID) {
            $questionTranslateableText = $questionText->getQuestion()->getTranslateableText();

            $questionTranslateableText->setKey(
                'qanda.ecmt-short-term.restricted-countries.question.ecmt-short-term-2020-apsg-without-sectors'
            );
        }

        return $questionText;
    }
}
