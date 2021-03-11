<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Date;

use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep as ApplicationStepEntity;
use Dvsa\Olcs\Api\Entity\Generic\Question as QuestionEntity;
use Dvsa\Olcs\Api\Service\Qa\AnswerSaver\GenericAnswerWriter;
use Dvsa\Olcs\Api\Service\Qa\QaContext;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\AnswerSaverInterface;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\GenericAnswerFetcher;
use Dvsa\Olcs\Api\Service\Qa\Supports\AnyTrait;

class DateAnswerSaver implements AnswerSaverInterface
{
    use AnyTrait;

    /** @var GenericAnswerWriter */
    private $genericAnswerWriter;

    /** @var GenericAnswerFetcher */
    private $genericAnswerFetcher;

    /**
     * Create service instance
     *
     * @param GenericAnswerWriter $genericAnswerWriter
     * @param GenericAnswerFetcher $genericAnswerFetcher
     *
     * @return DateAnswerSaver
     */
    public function __construct(GenericAnswerWriter $genericAnswerWriter, GenericAnswerFetcher $genericAnswerFetcher)
    {
        $this->genericAnswerWriter = $genericAnswerWriter;
        $this->genericAnswerFetcher = $genericAnswerFetcher;
    }

    /**
     * {@inheritdoc}
     */
    public function save(QaContext $qaContext, array $postData)
    {
        $applicationStepEntity = $qaContext->getApplicationStepEntity();

        $answerValue = $this->genericAnswerFetcher->fetch($applicationStepEntity, $postData);

        $this->genericAnswerWriter->write(
            $qaContext,
            $answerValue,
            QuestionEntity::QUESTION_TYPE_DATE
        );
    }
}
