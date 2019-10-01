<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\EcmtShortTerm;

use Dvsa\Olcs\Api\Domain\Repository\IrhpApplication as IrhpApplicationRepository;
use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep as ApplicationStepEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\AnswerClearerInterface;

class IntJourneysAnswerClearer implements AnswerClearerInterface
{
    /** @var IrhpApplicationRepository */
    private $irhpApplicationRepo;

    /**
     * Create service instance
     *
     * @param IrhpApplicationRepository $irhpApplicationRepo
     *
     * @return IntJourneysAnswerClearer
     */
    public function __construct(IrhpApplicationRepository $irhpApplicationRepo)
    {
        $this->irhpApplicationRepo = $irhpApplicationRepo;
    }

    /**
     * {@inheritdoc}
     */
    public function clear(ApplicationStepEntity $applicationStepEntity, IrhpApplicationEntity $irhpApplicationEntity)
    {
        $irhpApplicationEntity->clearInternationalJourneys();
        $this->irhpApplicationRepo->save($irhpApplicationEntity);
    }
}
