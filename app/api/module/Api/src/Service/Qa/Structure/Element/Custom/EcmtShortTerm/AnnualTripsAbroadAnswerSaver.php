<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\EcmtShortTerm;

use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep as ApplicationStepEntity;
use Dvsa\Olcs\Api\Entity\Generic\Question;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\AnswerSaverInterface;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\BaseAnswerSaver;

class AnnualTripsAbroadAnswerSaver implements AnswerSaverInterface
{
    /** @var BaseAnswerSaver */
    private $baseAnswerSaver;

    /**
     * Create service instance
     *
     * @param BaseAnswerSaver $baseAnswerSaver
     *
     * @return AnnualTripsAbroadAnswerSaver
     */
    public function __construct(BaseAnswerSaver $baseAnswerSaver)
    {
        $this->baseAnswerSaver = $baseAnswerSaver;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        ApplicationStepEntity $applicationStepEntity,
        IrhpApplicationEntity $irhpApplicationEntity,
        array $postData
    ) {
        $this->baseAnswerSaver->save(
            $applicationStepEntity,
            $irhpApplicationEntity,
            $postData,
            Question::QUESTION_TYPE_STRING
        );
    }
}
