<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal;

use Dvsa\Olcs\Api\Entity\Generic\Question;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Service\Qa\AnswerSaver\GenericAnswerWriter;
use Dvsa\Olcs\Api\Service\Qa\QaContextFactory;

class PermitUsageAnswerUpdater
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
     * @return PermitUsageAnswerUpdater
     */
    public function __construct(QaContextFactory $qaContextFactory, GenericAnswerWriter $genericAnswerWriter)
    {
        $this->qaContextFactory = $qaContextFactory;
        $this->genericAnswerWriter = $genericAnswerWriter;
    }

    /**
     * Create or update the answer value representing the permit usage selection
     *
     * @param IrhpPermitApplication $irhpPermitApplication
     * @param string $permitUsageSelection
     */
    public function update(IrhpPermitApplication $irhpPermitApplication, $permitUsageSelection)
    {
        $applicationStep = $irhpPermitApplication->getActiveApplicationPath()
            ->getApplicationStepByQuestionId(Question::QUESTION_ID_BILATERAL_PERMIT_USAGE);

        $qaContext = $this->qaContextFactory->create($applicationStep, $irhpPermitApplication);

        $this->genericAnswerWriter->write($qaContext, $permitUsageSelection);
    }
}
