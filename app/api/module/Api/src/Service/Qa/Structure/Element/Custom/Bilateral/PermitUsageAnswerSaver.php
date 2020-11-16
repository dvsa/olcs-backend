<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Bilateral;

use Dvsa\Olcs\Api\Service\Permits\Bilateral\Common\PermitUsageUpdater;
use Dvsa\Olcs\Api\Service\Qa\QaContext;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\AnswerSaverInterface;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\GenericAnswerFetcher;
use Dvsa\Olcs\Api\Service\Qa\Supports\IrhpPermitApplicationOnlyTrait;

class PermitUsageAnswerSaver implements AnswerSaverInterface
{
    use IrhpPermitApplicationOnlyTrait;

    /** @var GenericAnswerFetcher */
    private $genericAnswerFetcher;

    /** @var PermitUsageUpdater */
    private $permitUsageUpdater;

    /**
     * Create service instance
     *
     * @param GenericAnswerFetcher $genericAnswerFetcher
     * @param PermitUsageUpdater $permitUsageUpdater
     *
     * @return PermitUsageAnswerSaver
     */
    public function __construct(
        GenericAnswerFetcher $genericAnswerFetcher,
        PermitUsageUpdater $permitUsageUpdater
    ) {
        $this->genericAnswerFetcher = $genericAnswerFetcher;
        $this->permitUsageUpdater = $permitUsageUpdater;
    }

    /**
     * {@inheritdoc}
     */
    public function save(QaContext $qaContext, array $postData)
    {
        $newAnswer = $this->genericAnswerFetcher->fetch(
            $qaContext->getApplicationStepEntity(),
            $postData
        );

        $this->permitUsageUpdater->update($qaContext, $newAnswer);
    }
}
