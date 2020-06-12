<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Bilateral;

use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitApplication as IrhpPermitApplicationRepository;
use Dvsa\Olcs\Api\Service\Permits\Bilateral\ApplicationFeesClearer;
use Dvsa\Olcs\Api\Service\Qa\QaContext;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\AnswerClearerInterface;
use Dvsa\Olcs\Api\Service\Qa\Supports\IrhpPermitApplicationOnlyTrait;

class NoOfPermitsAnswerClearer implements AnswerClearerInterface
{
    use IrhpPermitApplicationOnlyTrait;

    /** @var ApplicationFeesClearer */
    private $applicationFeesClearer;

    /** @var IrhpPermitApplicationRepository */
    private $irhpPermitApplicationRepo;

    /**
     * Create service instance
     *
     * @param ApplicationFeesClearer $applicationFeesClearer
     * @param IrhpPermitApplicationRepository $irhpPermitApplicationRepo
     *
     * @return NoOfPermitsAnswerClearer
     */
    public function __construct(
        ApplicationFeesClearer $applicationFeesClearer,
        IrhpPermitApplicationRepository $irhpPermitApplicationRepo
    ) {
        $this->applicationFeesClearer = $applicationFeesClearer;
        $this->irhpPermitApplicationRepo = $irhpPermitApplicationRepo;
    }

    /**
     * {@inheritdoc}
     */
    public function clear(QaContext $qaContext)
    {
        $irhpPermitApplication = $qaContext->getQaEntity();

        $this->applicationFeesClearer->clear($irhpPermitApplication);

        $irhpPermitApplication->clearBilateralRequired();
        $this->irhpPermitApplicationRepo->save($irhpPermitApplication);
    }
}
