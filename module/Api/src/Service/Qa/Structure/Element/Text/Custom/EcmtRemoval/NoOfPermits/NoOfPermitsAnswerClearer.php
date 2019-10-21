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
     * @param IrhpPermitApplicationRepository $irhpPermitApplicationRepo
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
        $irhpPermitApplication = $irhpApplicationEntity->getFirstIrhpPermitApplication();
        $irhpPermitApplication->clearPermitsRequired();
        $this->irhpPermitApplicationRepo->save($irhpPermitApplication);
    }
}
