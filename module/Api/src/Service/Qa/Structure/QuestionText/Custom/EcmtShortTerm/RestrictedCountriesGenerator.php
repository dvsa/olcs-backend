<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText\Custom\EcmtShortTerm;

use Dvsa\Olcs\Api\Entity\Generic\ApplicationPathGroup;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;
use Dvsa\Olcs\Api\Service\Qa\QaContext;
use Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText\QuestionTextGenerator;
use Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText\QuestionTextGeneratorInterface;
use Dvsa\Olcs\Api\Service\Qa\Supports\IrhpApplicationOnlyTrait;
use RuntimeException;

class RestrictedCountriesGenerator implements QuestionTextGeneratorInterface
{
    use IrhpApplicationOnlyTrait;

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
    public function generate(QaContext $qaContext)
    {
        $translationKeyFragmentMappings = [
            IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT => 'ecmt-annual',
            IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM => 'ecmt-short-term',
        ];

        $irhpApplicationEntity = $qaContext->getQaEntity();
        $irhpPermitTypeId = $irhpApplicationEntity->getIrhpPermitType()->getId();
        if (!array_key_exists($irhpPermitTypeId, $translationKeyFragmentMappings)) {
            throw new RuntimeException('This question does not support permit type ' . $irhpPermitTypeId);
        }

        $translationKeyFragment = $translationKeyFragmentMappings[$irhpPermitTypeId];

        $questionKey = sprintf('qanda.%s.restricted-countries.question', $translationKeyFragment);
        $applicationPathGroupId = $irhpApplicationEntity->getAssociatedStock()->getApplicationPathGroup()->getId();
        if ($applicationPathGroupId == ApplicationPathGroup::ECMT_SHORT_TERM_2020_APSG_WITHOUT_SECTORS_ID) {
            $questionKey = 'qanda.ecmt-short-term.restricted-countries.question.ecmt-short-term-2020-apsg-without-sectors';
        }

        $guidanceKey = sprintf('qanda.%s.restricted-countries.guidance', $translationKeyFragment);

        $questionText = $this->questionTextGenerator->generate($qaContext);
        $questionText->getQuestion()->getTranslateableText()->setKey($questionKey);
        $questionText->getGuidance()->getTranslateableText()->setKey($guidanceKey);

        return $questionText;
    }
}
