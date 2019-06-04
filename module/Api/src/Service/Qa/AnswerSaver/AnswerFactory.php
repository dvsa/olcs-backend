<?php

namespace Dvsa\Olcs\Api\Service\Qa\AnswerSaver;

use Dvsa\Olcs\Api\Entity\Generic\Answer;
use Dvsa\Olcs\Api\Entity\Generic\QuestionText;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;

class AnswerFactory
{
    /**
     * Create an Answer entity instance referencing the supplied parameters
     *
     * @param QuestionText $questionText
     * @param IrhpApplication $irhpApplication
     *
     * @return Answer
     */
    public function create(QuestionText $questionText, IrhpApplication $irhpApplication)
    {
        return Answer::createNewForIrhpApplication($questionText, $irhpApplication);
    }
}
