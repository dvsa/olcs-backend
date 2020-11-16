<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal;

use Dvsa\Olcs\Api\Service\Qa\AnswerSaver\GenericAnswerWriter;
use Dvsa\Olcs\Api\Service\Qa\QaContext;

class FixedAnswerQuestionHandler implements QuestionHandlerInterface
{
    /** @var GenericAnswerWriter */
    private $genericAnswerWriter;

    /** @var string */
    private $answer;

    /**
     * Create service instance
     *
     * @param GenericAnswerWriter $genericAnswerWriter
     * @param string $answer
     *
     * @return FixedAnswerQuestionHandler
     */
    public function __construct(GenericAnswerWriter $genericAnswerWriter, $answer)
    {
        $this->genericAnswerWriter = $genericAnswerWriter;
        $this->answer = $answer;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(QaContext $qaContext, array $requiredPermits)
    {
        $this->genericAnswerWriter->write($qaContext, $this->answer);
    }
}
