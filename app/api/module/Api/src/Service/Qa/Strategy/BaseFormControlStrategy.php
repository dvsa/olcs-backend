<?php

namespace Dvsa\Olcs\Api\Service\Qa\Strategy;

use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep;
use Dvsa\Olcs\Api\Service\Qa\AnswersSummary\AnswerSummaryProviderInterface;
use Dvsa\Olcs\Api\Service\Qa\QaContext;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\AnswerClearerInterface;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\AnswerSaverInterface;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementGeneratorContext;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementGeneratorInterface;
use Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText\QuestionTextGeneratorInterface;
use RuntimeException;

class BaseFormControlStrategy implements FormControlStrategyInterface
{
    /** @var string */
    private $frontendType;

    /** @var ElementGeneratorInterface */
    private $elementGenerator;

    /** @var AnswerSaverInterface */
    private $answerSaver;

    /** @var AnswerClearerInterface */
    private $answerClearer;

    /** @var QuestionTextGeneratorInterface */
    private $questionTextGenerator;

    /** @var AnswerSummaryProviderInterface */
    private $answerSummaryProvider;

    /**
     * Create service instance
     *
     * @param string $frontendType
     * @param ElementGeneratorInterface $elementGenerator
     * @param AnswerSaverInterface $answerSaver
     * @param AnswerClearerInterface $answerClearer
     * @param QuestionTextGeneratorInterface $questionTextGenerator
     * @param AnswerSummaryProviderInterface $answerSummaryProvider
     *
     * @return BaseFormControlStrategy
     */
    public function __construct(
        $frontendType,
        ElementGeneratorInterface $elementGenerator,
        AnswerSaverInterface $answerSaver,
        AnswerClearerInterface $answerClearer,
        QuestionTextGeneratorInterface $questionTextGenerator,
        AnswerSummaryProviderInterface $answerSummaryProvider
    ) {
        $this->frontendType = $frontendType;
        $this->elementGenerator = $elementGenerator;
        $this->answerSaver = $answerSaver;
        $this->answerClearer = $answerClearer;
        $this->questionTextGenerator = $questionTextGenerator;
        $this->answerSummaryProvider = $answerSummaryProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getFrontendType()
    {
        return $this->frontendType;
    }

    /**
     * {@inheritdoc}
     */
    public function getElement(ElementGeneratorContext $context)
    {
        $qaEntity = $context->getQaEntity();

        if (!$this->elementGenerator->supports($qaEntity)) {
            throw new RuntimeException('Element generator does not support entity type');
        }

        return $this->elementGenerator->generate($context);
    }

    /**
     * {@inheritdoc}
     */
    public function saveFormData(QaContext $qaContext, array $postData)
    {
        $qaEntity = $qaContext->getQaEntity();

        if (!$this->answerSaver->supports($qaEntity)) {
            throw new RuntimeException('Answer saver does not support entity type');
        }

        $this->answerSaver->save($qaContext, $postData);
    }

    /**
     * {@inheritdoc}
     */
    public function clearAnswer(QaContext $qaContext)
    {
        $qaEntity = $qaContext->getQaEntity();

        if (!$this->answerClearer->supports($qaEntity)) {
            throw new RuntimeException('Answer clearer does not support entity type');
        }

        $this->answerClearer->clear($qaContext);
    }

    /**
     * {@inheritdoc}
     */
    public function getQuestionText(QaContext $qaContext)
    {
        $qaEntity = $qaContext->getQaEntity();

        if (!$this->questionTextGenerator->supports($qaEntity)) {
            throw new RuntimeException('Question text generator does not support entity type');
        }

        return $this->questionTextGenerator->generate($qaContext);
    }

    /**
     * {@inheritdoc}
     */
    public function getAnswerSummaryProvider()
    {
        return $this->answerSummaryProvider;
    }
}
