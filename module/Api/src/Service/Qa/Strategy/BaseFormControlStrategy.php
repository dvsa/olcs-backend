<?php

namespace Dvsa\Olcs\Api\Service\Qa\Strategy;

use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\AnswerClearerInterface;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\AnswerSaverInterface;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementGeneratorContext;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementGeneratorInterface;
use Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText\QuestionTextGeneratorContext;
use Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText\QuestionTextGeneratorInterface;
use Dvsa\Olcs\Api\Service\Qa\Structure\SelfservePage;

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

    /**
     * Create service instance
     *
     * @param string $frontendType
     * @param ElementGeneratorInterface $elementGenerator
     * @param AnswerSaverInterface $answerSaver
     * @param AnswerClearerInterface $answerClearer
     * @param QuestionTextGeneratorInterface $questionTextGenerator
     *
     * @return BaseFormControlStrategy
     */
    public function __construct(
        $frontendType,
        ElementGeneratorInterface $elementGenerator,
        AnswerSaverInterface $answerSaver,
        AnswerClearerInterface $answerClearer,
        QuestionTextGeneratorInterface $questionTextGenerator
    ) {
        $this->frontendType = $frontendType;
        $this->elementGenerator = $elementGenerator;
        $this->answerSaver = $answerSaver;
        $this->answerClearer = $answerClearer;
        $this->questionTextGenerator = $questionTextGenerator;
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
        return $this->elementGenerator->generate($context);
    }

    /**
     * {@inheritdoc}
     */
    public function saveFormData(ApplicationStep $applicationStep, IrhpApplication $irhpApplication, array $postData)
    {
        $this->answerSaver->save($applicationStep, $irhpApplication, $postData);
    }

    /**
     * {@inheritdoc}
     */
    public function clearAnswer(ApplicationStep $applicationStep, IrhpApplication $irhpApplication)
    {
        $this->answerClearer->clear($applicationStep, $irhpApplication);
    }

    /**
     * {@inheritdoc}
     */
    public function getQuestionText(QuestionTextGeneratorContext $questionTextGeneratorContext)
    {
        return $this->questionTextGenerator->generate($questionTextGeneratorContext);
    }
}
