<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element;

use Dvsa\Olcs\Api\Entity\Generic\Answer;
use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Service\Qa\AnswerSaver\GenericAnswerWriter;

class GenericAnswerSaver implements AnswerSaverInterface
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
     * @return GenericAnswerSaver
     */
    public function __construct(GenericAnswerWriter $genericAnswerWriter, GenericAnswerFetcher $genericAnswerFetcher)
    {
        $this->genericAnswerWriter = $genericAnswerWriter;
        $this->genericAnswerFetcher = $genericAnswerFetcher;
    }

    /**
     * {@inheritdoc}
     */
    public function save(ApplicationStep $applicationStep, IrhpApplication $irhpApplication, array $postData)
    {
        $this->genericAnswerWriter->write(
            $applicationStep,
            $irhpApplication,
            $this->genericAnswerFetcher->fetch($applicationStep, $postData)
        );
    }
}
