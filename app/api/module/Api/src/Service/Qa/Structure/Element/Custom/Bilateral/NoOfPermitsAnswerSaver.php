<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Bilateral;

use Dvsa\Olcs\Api\Service\Qa\QaContext;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\AnswerSaverInterface;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Bilateral\NoOfPermitsAvailableTextboxes as AvailableTextboxes;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\NamedAnswerFetcher;
use Dvsa\Olcs\Api\Service\Qa\Supports\IrhpPermitApplicationOnlyTrait;

class NoOfPermitsAnswerSaver implements AnswerSaverInterface
{
    use IrhpPermitApplicationOnlyTrait;

    /** @var NamedAnswerFetcher */
    private $namedAnswerFetcher;

    /** @var NoOfPermitsUpdater */
    private $noOfPermitsUpdater;

    /**
     * Create service instance
     *
     * @param NamedAnswerFetcher $namedAnswerFetcher
     * @param NoOfPermitsUpdater $noOfPermitsUpdater
     *
     * @return NoOfPermitsAnswerSaver
     */
    public function __construct(NamedAnswerFetcher $namedAnswerFetcher, NoOfPermitsUpdater $noOfPermitsUpdater)
    {
        $this->namedAnswerFetcher = $namedAnswerFetcher;
        $this->noOfPermitsUpdater = $noOfPermitsUpdater;
    }

    /**
     * {@inheritdoc}
     */
    public function save(QaContext $qaContext, array $postData)
    {
        $irhpPermitApplication = $qaContext->getQaEntity();
        $oldAnswers = $irhpPermitApplication->getBilateralRequired();
        $updatedAnswers = $irhpPermitApplication->getDefaultBilateralRequired();

        $cabotageAnswer = $irhpPermitApplication->getBilateralCabotageSelection();
        $availableTextboxes = AvailableTextBoxes::LOOKUP[$cabotageAnswer];
        $applicationStep = $qaContext->getApplicationStepEntity();

        foreach ($availableTextboxes as $standardOrCabotageKey) {
            $answerValue = $this->namedAnswerFetcher->fetch($applicationStep, $postData, $standardOrCabotageKey);
            $updatedAnswers[$standardOrCabotageKey] = $answerValue;
        }

        if ($oldAnswers == $updatedAnswers) {
            return;
        }

        $this->noOfPermitsUpdater->update($irhpPermitApplication, $updatedAnswers);
    }
}
