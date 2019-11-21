<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element;

use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Service\Qa\AnswerSaver\GenericAnswerWriter;

class BaseAnswerSaver
{
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
     * @return BaseAnswerSaver
     */
    public function __construct(GenericAnswerWriter $genericAnswerWriter, GenericAnswerFetcher $genericAnswerFetcher)
    {
        $this->genericAnswerWriter = $genericAnswerWriter;
        $this->genericAnswerFetcher = $genericAnswerFetcher;
    }

    /**
     * Save an answer to persistent storage, optionally specifying the type of data being stored
     *
     * @param ApplicationStep $applicationStep
     * @param IrhpApplication $irhpApplication
     * @param array $postData
     * @param string|null $forceQuestionType
     */
    public function save(
        ApplicationStep $applicationStep,
        IrhpApplication $irhpApplication,
        array $postData,
        $forceQuestionType = null
    ) {
        $this->genericAnswerWriter->write(
            $applicationStep,
            $irhpApplication,
            $this->genericAnswerFetcher->fetch($applicationStep, $postData),
            $forceQuestionType
        );
    }
}
