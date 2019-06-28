<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Text\Custom\EcmtRemoval\NoOfPermits;

use Dvsa\Olcs\Api\Service\Qa\Structure\Element\AnswerSaverInterface;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\GenericAnswerFetcher;
use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep as ApplicationStepEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;

class NoOfPermitsAnswerSaver implements AnswerSaverInterface
{
    /** @var GenericAnswerFetcher */
    private $genericAnswerFetcher;

    /** @var AnswerWriter */
    private $answerWriter;

    /** @var FeeCreator */
    private $feeCreator;

    /**
     * Create service instance
     *
     * @param GenericAnswerFetcher $genericAnswerFetcher
     * @param AnswerWriter $answerWriter
     * @param FeeCreator $feeCreator
     *
     * @return NoOfPermitsAnswerSaver
     */
    public function __construct(
        GenericAnswerFetcher $genericAnswerFetcher,
        AnswerWriter $answerWriter,
        FeeCreator $feeCreator
    ) {
        $this->genericAnswerFetcher = $genericAnswerFetcher;
        $this->answerWriter = $answerWriter;
        $this->feeCreator = $feeCreator;
    }

    /**
     * Write the number of permits required to persistent storage and cancel/create fees as appropriate
     *
     * @param ApplicationStepEntity $applicationStepEntity
     * @param IrhpApplicationEntity $irhpApplicationEntity
     * @param array $postData
     */
    public function save(
        ApplicationStepEntity $applicationStepEntity,
        IrhpApplicationEntity $irhpApplicationEntity,
        array $postData
    ) {
        $permitsRequired = $this->genericAnswerFetcher->fetch($applicationStepEntity, $postData);
        $this->answerWriter->write($irhpApplicationEntity, $permitsRequired);
        $this->feeCreator->create($irhpApplicationEntity, $permitsRequired);
    }
}
