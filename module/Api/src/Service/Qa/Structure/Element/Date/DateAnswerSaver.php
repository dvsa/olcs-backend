<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Date;

use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep as ApplicationStepEntity;
use Dvsa\Olcs\Api\Entity\Generic\Question as QuestionEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use Dvsa\Olcs\Api\Service\Qa\AnswerSaver\GenericAnswerWriter;
use Dvsa\Olcs\Api\Service\Qa\Common\DateTimeFactory;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\AnswerSaverInterface;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\GenericAnswerFetcher;

class DateAnswerSaver implements AnswerSaverInterface
{
    /** @var GenericAnswerWriter */
    private $genericAnswerWriter;

    /** @var GenericAnswerFetcher */
    private $genericAnswerFetcher;

    /** @var DateTimeFactory */
    private $dateTimeFactory;

    /**
     * Create service instance
     *
     * @param GenericAnswerWriter $genericAnswerWriter
     * @param GenericAnswerFetcher $genericAnswerFetcher
     * @param DateTimeFactory $dateTimeFactory
     *
     * @return DateAnswerSaver
     */
    public function __construct(
        GenericAnswerWriter $genericAnswerWriter,
        GenericAnswerFetcher $genericAnswerFetcher,
        DateTimeFactory $dateTimeFactory
    ) {
        $this->genericAnswerWriter = $genericAnswerWriter;
        $this->genericAnswerFetcher = $genericAnswerFetcher;
        $this->dateTimeFactory = $dateTimeFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        ApplicationStepEntity $applicationStepEntity,
        IrhpApplicationEntity $irhpApplicationEntity,
        array $postData
    ) {
        $dateString = $this->genericAnswerFetcher->fetch($applicationStepEntity, $postData);

        if (is_array($dateString)) {
            $dateString = sprintf(
                '%s-%s-%s',
                $dateString['year'],
                $dateString['month'],
                $dateString['day']
            );
        }

        $answerValue = $this->dateTimeFactory->create($dateString);

        $this->genericAnswerWriter->write(
            $applicationStepEntity,
            $irhpApplicationEntity,
            $answerValue,
            QuestionEntity::QUESTION_TYPE_DATE
        );
    }
}
