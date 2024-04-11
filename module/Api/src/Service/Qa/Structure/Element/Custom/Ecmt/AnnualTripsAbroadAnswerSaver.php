<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Ecmt;

use Dvsa\Olcs\Api\Entity\Generic\Question;
use Dvsa\Olcs\Api\Service\Qa\QaContext;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\AnswerSaverInterface;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\BaseAnswerSaver;
use Dvsa\Olcs\Api\Service\Qa\Supports\AnyTrait;

class AnnualTripsAbroadAnswerSaver implements AnswerSaverInterface
{
    use AnyTrait;

    /**
     * Create service instance
     *
     *
     * @return AnnualTripsAbroadAnswerSaver
     */
    public function __construct(private BaseAnswerSaver $baseAnswerSaver)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function save(QaContext $qaContext, array $postData)
    {
        $this->baseAnswerSaver->save($qaContext, $postData, Question::QUESTION_TYPE_STRING);
    }
}
