<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Bilateral;

use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Service\Qa\QaContext;
use Dvsa\Olcs\Api\Service\Permits\Bilateral\Common\NoOfPermitsConditionalUpdater;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\AnswerSaverInterface;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\GenericAnswerFetcher;
use Dvsa\Olcs\Api\Service\Qa\Supports\IrhpPermitApplicationOnlyTrait;

class NoOfPermitsMoroccoAnswerSaver implements AnswerSaverInterface
{
    use IrhpPermitApplicationOnlyTrait;

    /** @var GenericAnswerFetcher */
    private $genericAnswerFetcher;

    /** @var NoOfPermitsConditionalUpdater */
    private $noOfPermitsConditionalUpdater;

    /**
     * Create service instance
     *
     * @param GenericAnswerFetcher $genericAnswerFetcher
     * @param NoOfPermitsConditionalUpdater $noOfPermitsConditionalUpdater
     *
     * @return NoOfPermitsMoroccoAnswerSaver
     */
    public function __construct(
        GenericAnswerFetcher $genericAnswerFetcher,
        NoOfPermitsConditionalUpdater $noOfPermitsConditionalUpdater
    ) {
        $this->genericAnswerFetcher = $genericAnswerFetcher;
        $this->noOfPermitsConditionalUpdater = $noOfPermitsConditionalUpdater;
    }

    /**
     * {@inheritdoc}
     */
    public function save(QaContext $qaContext, array $postData)
    {
        $irhpPermitApplication = $qaContext->getQaEntity();

        $updatedAnswers = IrhpPermitApplication::DEFAULT_BILATERAL_REQUIRED;
        $updatedAnswers[IrhpPermitApplication::BILATERAL_MOROCCO_REQUIRED] = $this->genericAnswerFetcher->fetch(
            $qaContext->getApplicationStepEntity(),
            $postData
        );

        $this->noOfPermitsConditionalUpdater->update($irhpPermitApplication, $updatedAnswers);
    }
}
