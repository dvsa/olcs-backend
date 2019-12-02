<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Checkbox;

use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep as ApplicationStepEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\GenericAnswerFetcher;
use Dvsa\Olcs\Api\Service\Qa\AnswerSaver\GenericAnswerWriter;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\AnswerSaverInterface;

class CheckboxAnswerSaver implements AnswerSaverInterface
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
     * @return CheckboxAnswerSaver
     */
    public function __construct(GenericAnswerWriter $genericAnswerWriter, GenericAnswerFetcher $genericAnswerFetcher)
    {
        $this->genericAnswerWriter = $genericAnswerWriter;
        $this->genericAnswerFetcher = $genericAnswerFetcher;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        ApplicationStepEntity $applicationStepEntity,
        IrhpApplicationEntity $irhpApplicationEntity,
        array $postData
    ) {
        $answerValue = true;
        try {
            $this->genericAnswerFetcher->fetch($applicationStepEntity, $postData);
        } catch (NotFoundException $e) {
            $answerValue = false;
        }

        $this->genericAnswerWriter->write($applicationStepEntity, $irhpApplicationEntity, $answerValue);
    }
}
