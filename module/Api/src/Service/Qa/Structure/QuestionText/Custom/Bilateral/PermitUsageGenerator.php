<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText\Custom\Bilateral;

use Dvsa\Olcs\Api\Service\Qa\QaContext;
use Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText\QuestionTextGenerator;
use Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText\QuestionTextGeneratorInterface;
use Dvsa\Olcs\Api\Service\Qa\Supports\IrhpPermitApplicationOnlyTrait;

class PermitUsageGenerator implements QuestionTextGeneratorInterface
{
    use IrhpPermitApplicationOnlyTrait;

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
    public function generate(QaContext $qaContext)
    {
        $irhpPermitApplicationEntity = $qaContext->getQaEntity();

        $permitUsageList = $irhpPermitApplicationEntity->getIrhpPermitWindow()
            ->getIrhpPermitStock()
            ->getPermitUsageList();

        $questionText = $this->questionTextGenerator->generate($qaContext);

        if (count($permitUsageList) == 1) {
            $questionText->getQuestion()
                ->getTranslateableText()
                ->setKey('qanda.bilaterals.permit-usage.question.single-option');
        }

        return $questionText;
    }
}
