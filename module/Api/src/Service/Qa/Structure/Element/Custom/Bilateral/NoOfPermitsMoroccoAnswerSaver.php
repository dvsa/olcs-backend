<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Bilateral;

use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Service\Qa\QaContext;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\AnswerSaverInterface;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\GenericAnswerFetcher;
use Dvsa\Olcs\Api\Service\Qa\Supports\IrhpPermitApplicationOnlyTrait;

class NoOfPermitsMoroccoAnswerSaver implements AnswerSaverInterface
{
    use IrhpPermitApplicationOnlyTrait;

    /** @var GenericAnswerFetcher */
    private $genericAnswerFetcher;

    /** @var NoOfPermitsUpdater */
    private $noOfPermitsUpdater;

    /**
     * Create service instance
     *
     * @param GenericAnswerFetcher $genericAnswerFetcher
     * @param NoOfPermitsUpdater $noOfPermitsUpdater
     *
     * @return NoOfPermitsMoroccoAnswerSaver
     */
    public function __construct(GenericAnswerFetcher $genericAnswerFetcher, NoOfPermitsUpdater $noOfPermitsUpdater)
    {
        $this->genericAnswerFetcher = $genericAnswerFetcher;
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
        $updatedAnswers[IrhpPermitApplication::BILATERAL_MOROCCO_REQUIRED] = $this->genericAnswerFetcher->fetch(
            $qaContext->getApplicationStepEntity(),
            $postData
        );

        if ($oldAnswers == $updatedAnswers) {
            return;
        }

        $this->noOfPermitsUpdater->update($irhpPermitApplication, $updatedAnswers);
    }
}
