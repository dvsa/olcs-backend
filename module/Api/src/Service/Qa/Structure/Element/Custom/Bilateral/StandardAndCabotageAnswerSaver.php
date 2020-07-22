<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Bilateral;

use Dvsa\Olcs\Api\Entity\Generic\Answer;
use Dvsa\Olcs\Api\Entity\Generic\Question;
use Dvsa\Olcs\Api\Service\Qa\AnswerSaver\GenericAnswerWriter;
use Dvsa\Olcs\Api\Service\Qa\QaContext;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\AnswerSaverInterface;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\NamedAnswerFetcher;
use Dvsa\Olcs\Api\Service\Qa\Supports\IrhpPermitApplicationOnlyTrait;

class StandardAndCabotageAnswerSaver implements AnswerSaverInterface
{
    use IrhpPermitApplicationOnlyTrait;

    /** @var NamedAnswerFetcher */
    private $namedAnswerFetcher;

    /** @var GenericAnswerWriter */
    private $genericAnswerWriter;

    /**
     * Create service instance
     *
     * @param NamedAnswerFetcher $namedAnswerFetcher
     * @param GenericAnswerWriter $genericAnswerWriter
     *
     * @return StandardAndCabotageAnswerSaver
     */
    public function __construct(NamedAnswerFetcher $namedAnswerFetcher, GenericAnswerWriter $genericAnswerWriter)
    {
        $this->namedAnswerFetcher = $namedAnswerFetcher;
        $this->genericAnswerWriter = $genericAnswerWriter;
    }

    /**
     * {@inheritdoc}
     */
    public function save(QaContext $qaContext, array $postData)
    {
        $applicationStepEntity = $qaContext->getApplicationStepEntity();

        $cabotageRequired = $this->namedAnswerFetcher->fetch(
            $applicationStepEntity,
            $postData,
            'qaElement'
        );

        $answerValue = Answer::BILATERAL_STANDARD_ONLY;
        if ($cabotageRequired == 'Y') {
            $answerValue = $this->namedAnswerFetcher->fetch(
                $applicationStepEntity,
                $postData,
                'yesContent'
            );
        }

        $this->genericAnswerWriter->write($qaContext, $answerValue);
    }
}
