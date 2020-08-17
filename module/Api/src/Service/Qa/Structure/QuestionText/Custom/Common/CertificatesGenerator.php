<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText\Custom\Common;

use Dvsa\Olcs\Api\Service\Qa\QaContext;
use Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText\QuestionTextGenerator;
use Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText\QuestionTextGeneratorInterface;
use Dvsa\Olcs\Api\Service\Qa\Supports\IrhpApplicationOnlyTrait;

class CertificatesGenerator implements QuestionTextGeneratorInterface
{
    use IrhpApplicationOnlyTrait;

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
    public function generate(QaContext $qaContext)
    {
        $questionText = $this->questionTextGenerator->generate($qaContext);

        if ($qaContext->getQaEntity()->getIrhpPermitType()->isEcmtRemoval()) {
            $questionText->getAdditionalGuidance()->getTranslateableText()->setKey(
                'qanda.common.certificates.additional-guidance.ecmt-removal'
            );
        }

        return $questionText;
    }
}