<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\EcmtShortTerm;

use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep as ApplicationStepEntity;
use Dvsa\Olcs\Api\Entity\Generic\Question;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use Dvsa\Olcs\Api\Service\Qa\AnswerSaver\GenericAnswerWriter;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\AnswerSaverInterface;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\GenericAnswerFetcher;

class AnnualTripsAbroadAnswerSaver implements AnswerSaverInterface
{
    /** @var GenericAnswerFetcher */
    private $genericAnswerFetcher;

    /** @var GenericAnswerWriter */
    private $genericAnswerWriter;

    /**
     * Create service instance
     *
     * @param GenericAnswerFetcher $genericAnswerFetcher
     * @param GenericAnswerWriter $genericAnswerWriter
     *
     * @return AnnualTripsAbroadAnswerSaver
     */
    public function __construct(GenericAnswerFetcher $genericAnswerFetcher, GenericAnswerWriter $genericAnswerWriter)
    {
        $this->genericAnswerFetcher = $genericAnswerFetcher;
        $this->genericAnswerWriter = $genericAnswerWriter;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        ApplicationStepEntity $applicationStepEntity,
        IrhpApplicationEntity $irhpApplicationEntity,
        array $postData
    ) {
        $answer = $this->genericAnswerFetcher->fetch($applicationStepEntity, $postData);

        $this->genericAnswerWriter->write(
            $applicationStepEntity,
            $irhpApplicationEntity,
            $answer,
            Question::QUESTION_TYPE_STRING
        );
    }
}
