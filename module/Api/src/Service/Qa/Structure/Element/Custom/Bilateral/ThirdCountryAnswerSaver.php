<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Bilateral;

use Dvsa\Olcs\Api\Service\Qa\AnswerSaver\GenericAnswerWriter;
use Dvsa\Olcs\Api\Service\Qa\QaContext;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\AnswerSaverInterface;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\GenericAnswerFetcher;
use Dvsa\Olcs\Api\Service\Qa\Supports\IrhpPermitApplicationOnlyTrait;

class ThirdCountryAnswerSaver implements AnswerSaverInterface
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
     * @return ThirdCountryAnswerSaver
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
        $transportingFromThirdCountry = $this->genericAnswerFetcher->fetch(
            $qaContext->getApplicationStepEntity(),
            $postData
        );

        if ($transportingFromThirdCountry == 'Y') {
            $this->genericAnswerWriter->write($qaContext, 'qanda.bilaterals.third-country.yes-answer');

            return;
        }

        return $this->clientReturnCodeHandler->handle($qaContext);
    }
}
