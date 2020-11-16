<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal;

use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitApplication as IrhpPermitApplicationRepository;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitStock as IrhpPermitStockRepository;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Service\Qa\AnswerSaver\ApplicationAnswersClearer;

class ExistingIrhpPermitApplicationHandler
{
    /** @var IrhpPermitApplicationRepository */
    private $irhpPermitApplicationRepo;

    /** @var IrhpPermitStockRepository */
    private $irhpPermitStockRepo;

    /** @var ApplicationAnswersClearer */
    private $applicationAnswersClearer;

    /** @var QuestionHandlerDelegator */
    private $questionHandlerDelegator;

    /**
     * Create service instance
     *
     * @param IrhpPermitApplicationRepository $irhpPermitApplicationRepo
     * @param IrhpPermitStockRepository $irhpPermitStockRepo
     * @param ApplicationAnswersClearer $applicationAnswersClearer
     * @param QuestionHandlerDelegator $questionHandlerDelegator
     *
     * @return ExistingIrhpPermitApplicationHandler
     */
    public function __construct(
        IrhpPermitApplicationRepository $irhpPermitApplicationRepo,
        IrhpPermitStockRepository $irhpPermitStockRepo,
        ApplicationAnswersClearer $applicationAnswersClearer,
        QuestionHandlerDelegator $questionHandlerDelegator
    ) {
        $this->irhpPermitApplicationRepo = $irhpPermitApplicationRepo;
        $this->irhpPermitStockRepo = $irhpPermitStockRepo;
        $this->applicationAnswersClearer = $applicationAnswersClearer;
        $this->questionHandlerDelegator = $questionHandlerDelegator;
    }

    /**
     * Handle the scenario where an irhp permit application already exists for a country
     *
     * @param IrhpPermitApplication $irhpPermitApplication
     * @param int $stockId
     * @param array $requiredPermits
     */
    public function handle(IrhpPermitApplication $irhpPermitApplication, $stockId, $requiredPermits)
    {
        $existingStockId = $irhpPermitApplication->getIrhpPermitWindow()->getIrhpPermitStock()->getId();
        if ($stockId != $existingStockId) {
            $this->applicationAnswersClearer->clear($irhpPermitApplication);

            $irhpPermitStock = $this->irhpPermitStockRepo->fetchById($stockId);
            $irhpPermitApplication->updateIrhpPermitWindow(
                $irhpPermitStock->getOpenWindow()
            );
        }

        $applicationSteps = $irhpPermitApplication->getActiveApplicationPath()->getApplicationSteps();
        foreach ($applicationSteps as $applicationStep) {
            $this->questionHandlerDelegator->delegate(
                $irhpPermitApplication,
                $applicationStep,
                $requiredPermits
            );
        }
        
        $irhpPermitApplication->updateCheckAnswers();
        $this->irhpPermitApplicationRepo->save($irhpPermitApplication);
    }
}
