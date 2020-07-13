<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal;

use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Service\Qa\AnswerSaver\GenericAnswerWriter;
use Dvsa\Olcs\Api\Service\Qa\QaContextFactory;

class GenericAnswerUpdater
{
    /** @var QaContextFactory */
    private $qaContextFactory;

    /** @var GenericAnswerWriter */
    private $genericAnswerWriter;

    /**
     * Create service instance
     *
     * @param QaContextFactory $qaContextFactory
     * @param GenericAnswerWriter $genericAnswerWriter
     *
     * @return GenericAnswerUpdater
     */
    public function __construct(QaContextFactory $qaContextFactory, GenericAnswerWriter $genericAnswerWriter)
    {
        $this->qaContextFactory = $qaContextFactory;
        $this->genericAnswerWriter = $genericAnswerWriter;
    }

    /**
     * Write an answer to a question in the context of the specified application and it's associated application
     * path
     *
     * @param IrhpPermitApplication $irhpPermitApplication
     * @param int $questionId
     * @param string $answerValue
     */
    public function update(IrhpPermitApplication $irhpPermitApplication, $questionId, $answerValue)
    {
        $applicationPath = $irhpPermitApplication->getActiveApplicationPath();

        $qaContext = $this->qaContextFactory->create(
            $applicationPath->getApplicationStepByQuestionId($questionId),
            $irhpPermitApplication
        );

        $this->genericAnswerWriter->write($qaContext, $answerValue);
    }
}
