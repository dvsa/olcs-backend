<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\EcmtShortTerm;

use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitApplication as IrhpPermitApplicationRepository;
use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep as ApplicationStepEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\AnswerSaverInterface;

class NoOfPermitsAnswerSaver implements AnswerSaverInterface
{
    /** @var IrhpPermitApplicationRepository */
    private $irhpPermitApplicationRepo;

    /** @var NoOfPermitsAnswerFetcher */
    private $noOfPermitsAnswerFetcher;

    /**
     * Create service instance
     *
     * @param IrhpPermitApplicationRepository $irhpPermitApplicationRepo
     * @param NoOfPermitsAnswerFetcher $noOfPermitsAnswerFetcher
     *
     * @return NoOfPermitsAnswerSaver
     */
    public function __construct(
        IrhpPermitApplicationRepository $irhpPermitApplicationRepo,
        NoOfPermitsAnswerFetcher $noOfPermitsAnswerFetcher
    ) {
        $this->irhpPermitApplicationRepo = $irhpPermitApplicationRepo;
        $this->noOfPermitsAnswerFetcher = $noOfPermitsAnswerFetcher;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        ApplicationStepEntity $applicationStepEntity,
        IrhpApplicationEntity $irhpApplicationEntity,
        array $postData
    ) {
        $requiredEuro5 = $this->noOfPermitsAnswerFetcher->fetch(
            $applicationStepEntity,
            $postData,
            FieldNames::REQUIRED_EURO5
        );

        $requiredEuro6 = $this->noOfPermitsAnswerFetcher->fetch(
            $applicationStepEntity,
            $postData,
            FieldNames::REQUIRED_EURO6
        );

        $irhpPermitApplication = $irhpApplicationEntity->getFirstIrhpPermitApplication();
        $irhpPermitApplication->updateEmissionsCategoryPermitsRequired($requiredEuro5, $requiredEuro6);
        $this->irhpPermitApplicationRepo->save($irhpPermitApplication);
    }
}
