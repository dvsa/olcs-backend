<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal;

use Dvsa\Olcs\Api\Service\Permits\Bilateral\Common\PermitUsageUpdater;
use Dvsa\Olcs\Api\Service\Qa\QaContext;

class PermitUsageQuestionHandler implements QuestionHandlerInterface
{
    /** @var PermitUsageSelectionGenerator */
    private $permitUsageSelectionGenerator;

    /** @var PermitUsageUpdater */
    private $permitUsageUpdater;

    /**
     * Create service instance
     *
     * @param PermitUsageSelectionGenerator $permitUsageSelectionGenerator
     * @param PermitUsageUpdater $permitUsageUpdater
     *
     * @return PermitUsageQuestionHandler
     */
    public function __construct(
        PermitUsageSelectionGenerator $permitUsageSelectionGenerator,
        PermitUsageUpdater $permitUsageUpdater
    ) {
        $this->permitUsageSelectionGenerator = $permitUsageSelectionGenerator;
        $this->permitUsageUpdater = $permitUsageUpdater;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(QaContext $qaContext, array $requiredPermits)
    {
        $newAnswer = $this->permitUsageSelectionGenerator->generate($requiredPermits);
        $this->permitUsageUpdater->update($qaContext, $newAnswer);
    }
}
