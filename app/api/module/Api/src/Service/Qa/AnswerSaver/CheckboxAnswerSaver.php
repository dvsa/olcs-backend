<?php

namespace Dvsa\Olcs\Api\Service\Qa\AnswerSaver;

use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;

class CheckboxAnswerSaver implements AnswerSaverInterface
{
    /** @var GenericAnswerWriter */
    private $genericAnswerWriter;

    /**
     * Create service instance
     *
     * @param GenericAnswerWriter $genericAnswerWriter
     *
     * @return GenericAnswerSaver
     */
    public function __construct(GenericAnswerWriter $genericAnswerWriter)
    {
        $this->genericAnswerWriter = $genericAnswerWriter;
    }

    /**
     * {@inheritdoc}
     */
    public function save(ApplicationStep $applicationStep, IrhpApplication $irhpApplication, array $postData)
    {
        $answerValue = false;
        if (isset($postData[$applicationStep->getFieldsetName()]['qaElement'])) {
            $answerValue = true;
        }

        $this->genericAnswerWriter->write($applicationStep, $irhpApplication, $answerValue);
    }
}
