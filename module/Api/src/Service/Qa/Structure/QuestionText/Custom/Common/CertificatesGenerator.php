<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText\Custom\Common;

use Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText\QuestionTextGenerator;
use Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText\QuestionTextGeneratorInterface;
use Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText\QuestionTextGeneratorContext;

class CertificatesGenerator implements QuestionTextGeneratorInterface
{
    /** @var QuestionTextGenerator */
    private $questionTextGenerator;

    /**
     * Create service instance
     *
     * @param QuestionTextGenerator $questionTextGenerator
     *
     * @return CertificatesGenerator
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

        if ($context->getIrhpApplicationEntity()->getIrhpPermitType()->isEcmtRemoval()) {
            $questionText->getAdditionalGuidance()->getTranslateableText()->setKey(
                'qanda.common.certificates.additional-guidance.ecmt-removal'
            );
        }

        return $questionText;
    }
}
