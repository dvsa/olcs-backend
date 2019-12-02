<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\EcmtShortTerm;

use Dvsa\Olcs\Api\Domain\Repository\IrhpApplication as IrhpApplicationRepository;
use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep as ApplicationStepEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\AnswerSaverInterface;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\GenericAnswerFetcher;

class IntJourneysAnswerSaver implements AnswerSaverInterface
{
    /** @var IrhpApplicationRepository */
    private $irhpApplicationRepo;

    /** @var GenericAnswerFetcher */
    private $genericAnswerFetcher;

    /**
     * Create service instance
     *
     * @param IrhpApplicationRepository $irhpApplicationRepo
     * @param GenericAnswerFetcher $genericAnswerFetcher
     *
     * @return IntJourneysAnswerSaver
     */
    public function __construct(
        IrhpApplicationRepository $irhpApplicationRepo,
        GenericAnswerFetcher $genericAnswerFetcher
    ) {
        $this->irhpApplicationRepo = $irhpApplicationRepo;
        $this->genericAnswerFetcher = $genericAnswerFetcher;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        ApplicationStepEntity $applicationStepEntity,
        IrhpApplicationEntity $irhpApplicationEntity,
        array $postData
    ) {
        $answer = $this->genericAnswerFetcher->fetch($applicationStepEntity, $postData);

        $irhpApplicationEntity->updateInternationalJourneys(
            $this->irhpApplicationRepo->getRefdataReference($answer)
        );

        $this->irhpApplicationRepo->save($irhpApplicationEntity);
    }
}
