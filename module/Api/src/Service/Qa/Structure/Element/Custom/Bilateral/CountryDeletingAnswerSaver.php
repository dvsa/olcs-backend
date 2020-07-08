<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Bilateral;

use Dvsa\Olcs\Api\Service\Qa\AnswerSaver\GenericAnswerWriter;
use Dvsa\Olcs\Api\Service\Qa\QaContext;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\AnswerSaverInterface;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\GenericAnswerFetcher;

class CountryDeletingAnswerSaver
{
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
     * @return CountryDeletingAnswerSaver
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
     * Save specified answer to persistent storage if yes, otherwise don't save answer and delete country from
     * application instead. Pass value back indicating what action needs to be taken by the client.
     *
     * @param QaContext $qaContext
     * @param array $postData
     * @param string $yesValue
     */
    public function save(QaContext $qaContext, array $postData, $yesValue)
    {
        $answer = $this->genericAnswerFetcher->fetch(
            $qaContext->getApplicationStepEntity(),
            $postData
        );

        if ($answer == 'Y') {
            $this->genericAnswerWriter->write($qaContext, $yesValue);

            return;
        }

        return $this->clientReturnCodeHandler->handle($qaContext);
    }
}
