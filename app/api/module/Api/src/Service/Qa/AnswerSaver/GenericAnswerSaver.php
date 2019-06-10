<?php

namespace Dvsa\Olcs\Api\Service\Qa\AnswerSaver;

use Dvsa\Olcs\Api\Entity\Generic\Answer;
use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;

class GenericAnswerSaver implements AnswerSaverInterface
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
        $value = $postData[$applicationStep->getFieldsetName()]['qaElement'];

        $this->genericAnswerWriter->write($applicationStep, $irhpApplication, $value);
    }
}
