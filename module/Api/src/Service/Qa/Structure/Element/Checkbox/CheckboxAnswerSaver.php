<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Checkbox;

use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Service\Qa\AnswerSaver\GenericAnswerWriter;
use Dvsa\Olcs\Api\Service\Qa\QaContext;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\AnswerSaverInterface;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\GenericAnswerFetcher;
use Dvsa\Olcs\Api\Service\Qa\Supports\AnyTrait;

class CheckboxAnswerSaver implements AnswerSaverInterface
{
    use AnyTrait;

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
    public function save(QaContext $qaContext, array $postData)
    {
        $answerValue = true;
        try {
            $this->genericAnswerFetcher->fetch(
                $qaContext->getApplicationStepEntity(),
                $postData
            );
        } catch (NotFoundException $e) {
            $answerValue = false;
        }

        $this->genericAnswerWriter->write($qaContext, $answerValue);
    }
}
