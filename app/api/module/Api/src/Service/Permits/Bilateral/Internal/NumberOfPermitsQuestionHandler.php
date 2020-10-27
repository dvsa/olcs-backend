<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal;

use Dvsa\Olcs\Api\Service\Permits\Bilateral\Common\NoOfPermitsConditionalUpdater;
use Dvsa\Olcs\Api\Service\Qa\QaContext;

class NumberOfPermitsQuestionHandler implements QuestionHandlerInterface
{
    /** @var PermitUsageSelectionGenerator */
    private $permitUsageSelectionGenerator;

    /** @var BilateralRequiredGenerator */
    private $bilateralRequiredGenerator;

    /** @var NoOfPermitsConditionalUpdater */
    private $noOfPermitsConditionalUpdater;

    /**
     * Create service instance
     *
     * @param PermitUsageSelectionGenerator $permitUsageSelectionGenerator
     * @param BilateralRequiredGenerator $bilateralRequiredGenerator
     * @param NoOfPermitsConditionalUpdater $noOfPermitsConditionalUpdater
     *
     * @return NumberOfPermitsQuestionHandler
     */
    public function __construct(
        PermitUsageSelectionGenerator $permitUsageSelectionGenerator,
        BilateralRequiredGenerator $bilateralRequiredGenerator,
        NoOfPermitsConditionalUpdater $noOfPermitsConditionalUpdater
    ) {
        $this->permitUsageSelectionGenerator = $permitUsageSelectionGenerator;
        $this->bilateralRequiredGenerator = $bilateralRequiredGenerator;
        $this->noOfPermitsConditionalUpdater = $noOfPermitsConditionalUpdater;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(QaContext $qaContext, array $requiredPermits)
    {
        $permitUsageSelection = $this->permitUsageSelectionGenerator->generate($requiredPermits);
        $bilateralRequired = $this->bilateralRequiredGenerator->generate($requiredPermits, $permitUsageSelection);

        $this->noOfPermitsConditionalUpdater->update(
            $qaContext->getQaEntity(),
            $bilateralRequired
        );
    }
}
