<?php

namespace Dvsa\Olcs\Api\Service\Qa\Strategy;

use Dvsa\Olcs\Api\Entity\Generic\Answer;
use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Service\Qa\AnswerSaver\AnswerSaverInterface;
use Dvsa\Olcs\Api\Service\Qa\Element\ElementGeneratorInterface;
use Dvsa\Olcs\Api\Service\Qa\PostProcessor\SelfservePagePostProcessorInterface;
use Dvsa\Olcs\Api\Service\Qa\Element\SelfservePage;

class BaseFormControlStrategy implements FormControlStrategyInterface
{
    /** @var string */
    private $frontendType;

    /** @var ElementGeneratorInterface */
    private $elementGenerator;

    /** @var AnswerSaverInterface */
    private $answerSaver;

    /** @var SelfservePagePostProcessorInterface */
    private $selfservePagePostProcessor;

    /**
     * Create service instance
     *
     * @param string $frontendType
     * @param ElementGeneratorInterface $elementGenerator
     * @param AnswerSaverInterface $answerSaver
     * @param SelfservePagePostProcessorInterface $selfservePagePostProcessor
     *
     * @return BaseFormControlStrategy
     */
    public function __construct(
        $frontendType,
        ElementGeneratorInterface $elementGenerator,
        AnswerSaverInterface $answerSaver,
        SelfservePagePostProcessorInterface $selfservePagePostProcessor
    ) {
        $this->frontendType = $frontendType;
        $this->elementGenerator = $elementGenerator;
        $this->answerSaver = $answerSaver;
        $this->selfservePagePostProcessor = $selfservePagePostProcessor;
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
    public function getElement(
        ApplicationStep $applicationStep,
        IrhpApplication $irhpApplication,
        ?Answer $answer = null
    ) {
        return $this->elementGenerator->generate($applicationStep, $irhpApplication, $answer);
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
    public function postProcessSelfservePage(SelfservePage $page)
    {
        $this->selfservePagePostProcessor->process($page);
    }
}
