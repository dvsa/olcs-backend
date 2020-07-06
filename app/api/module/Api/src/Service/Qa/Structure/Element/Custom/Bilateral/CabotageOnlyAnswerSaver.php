<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Bilateral;

use Dvsa\Olcs\Api\Entity\Generic\Answer;
use Dvsa\Olcs\Api\Entity\Generic\Question;
use Dvsa\Olcs\Api\Service\Qa\AnswerSaver\GenericAnswerWriter;
use Dvsa\Olcs\Api\Service\Qa\QaContext;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\AnswerSaverInterface;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\GenericAnswerFetcher;
use Dvsa\Olcs\Api\Service\Qa\Supports\IrhpPermitApplicationOnlyTrait;

class CabotageOnlyAnswerSaver implements AnswerSaverInterface
{
    use IrhpPermitApplicationOnlyTrait;

    /** @var GenericAnswerFetcher */
    private $genericAnswerFetcher;

    /** @var GenericAnswerWriter */
    private $genericAnswerWriter;

    /** @var ClientReturnCodeHandler */
    private $clientReturnCodeHandler;

    /**
     * Create service instance
     *
     * @param GenericAnswerFetcher $genericAnswerFetcher
     * @param GenericAnswerWriter $genericAnswerWriter
     * @param ClientReturnCodeHandler $clientReturnCodeHandler
     *
     * @return CabotageOnlyAnswerSaver
     */
    public function __construct(
        GenericAnswerFetcher $genericAnswerFetcher,
        GenericAnswerWriter $genericAnswerWriter,
        ClientReturnCodeHandler $clientReturnCodeHandler
    ) {
        $this->genericAnswerFetcher = $genericAnswerFetcher;
        $this->genericAnswerWriter = $genericAnswerWriter;
        $this->clientReturnCodeHandler = $clientReturnCodeHandler;
    }

    /**
     * {@inheritdoc}
     */
    public function save(QaContext $qaContext, array $postData)
    {
        $cabotageRequired = $this->genericAnswerFetcher->fetch(
            $qaContext->getApplicationStepEntity(),
            $postData
        );

        if ($cabotageRequired == 'Y') {
            $this->genericAnswerWriter->write(
                $qaContext,
                Answer::BILATERAL_CABOTAGE_ONLY,
                Question::QUESTION_TYPE_STRING
            );

            return;
        }

        return $this->clientReturnCodeHandler->handle($qaContext);
    }
}
