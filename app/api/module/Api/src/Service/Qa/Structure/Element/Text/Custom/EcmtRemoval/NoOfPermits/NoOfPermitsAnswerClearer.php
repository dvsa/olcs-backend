<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Text\Custom\EcmtRemoval\NoOfPermits;

use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitApplication as IrhpPermitApplicationRepository;
use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep as ApplicationStepEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\AnswerClearerInterface;

class NoOfPermitsAnswerClearer implements AnswerClearerInterface
{
    /** @var IrhpPermitApplicationRepository */
    private $irhpPermitApplicationRepo;

    /**
     * Create service instance
     *
     * @param AnswerRepository $answerRepo
     *
     * @return NoOfPermitsAnswerClearer
     */
    public function __construct(IrhpPermitApplicationRepository $irhpPermitApplicationRepo)
    {
        $this->irhpPermitApplicationRepo = $irhpPermitApplicationRepo;
    }

    /**
     * {@inheritdoc}
     */
    public function clear(ApplicationStepEntity $applicationStepEntity, IrhpApplicationEntity $irhpApplicationEntity)
    {
        $irhpPermitApplications = $irhpApplicationEntity->getIrhpPermitApplications();
        if ($irhpPermitApplications->count()) {
            $irhpPermitApplication = $irhpPermitApplications->first();
            $this->irhpPermitApplicationRepo->delete($irhpPermitApplication);
        }
    }
}
