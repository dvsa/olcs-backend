<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Text\Custom\EcmtRemoval\NoOfPermits;

use Dvsa\Olcs\Api\Service\Qa\QaContext;
use Dvsa\Olcs\Api\Service\Qa\Supports\IrhpApplicationOnlyTrait;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\AnswerSaverInterface;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\GenericAnswerFetcher;

class NoOfPermitsAnswerSaver implements AnswerSaverInterface
{
    use IrhpApplicationOnlyTrait;

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
     * @param QaContext $qaContext
     * @param array $postData
     */
    public function save(QaContext $qaContext, array $postData)
    {
        $permitsRequired = $this->genericAnswerFetcher->fetch(
            $qaContext->getApplicationStepEntity(),
            $postData
        );

        $irhpApplicationEntity = $qaContext->getQaEntity();
        $this->answerWriter->write($irhpApplicationEntity, $permitsRequired);
        $this->feeCreator->create($irhpApplicationEntity, $permitsRequired);
    }
}
