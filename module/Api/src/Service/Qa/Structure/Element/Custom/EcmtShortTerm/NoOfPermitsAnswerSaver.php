<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\EcmtShortTerm;

use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitApplication as IrhpPermitApplicationRepository;
use Dvsa\Olcs\Api\Service\Qa\QaContext;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\AnswerSaverInterface;
use Dvsa\Olcs\Api\Service\Qa\Supports\IrhpApplicationOnlyTrait;

class NoOfPermitsAnswerSaver implements AnswerSaverInterface
{
    use IrhpApplicationOnlyTrait;

    /** @var IrhpPermitApplicationRepository */
    private $irhpPermitApplicationRepo;

    /** @var NoOfPermitsAnswerFetcher */
    private $noOfPermitsAnswerFetcher;

    /** @var ConditionalFeeUpdater */
    private $conditionalFeeUpdater;

    /**
     * Create service instance
     *
     * @param IrhpPermitApplicationRepository $irhpPermitApplicationRepo
     * @param NoOfPermitsAnswerFetcher $noOfPermitsAnswerFetcher
     * @param ConditionalFeeUpdater $conditionalFeeUpdater
     *
     * @return NoOfPermitsAnswerSaver
     */
    public function __construct(
        IrhpPermitApplicationRepository $irhpPermitApplicationRepo,
        NoOfPermitsAnswerFetcher $noOfPermitsAnswerFetcher,
        ConditionalFeeUpdater $conditionalFeeUpdater
    ) {
        $this->irhpPermitApplicationRepo = $irhpPermitApplicationRepo;
        $this->noOfPermitsAnswerFetcher = $noOfPermitsAnswerFetcher;
        $this->conditionalFeeUpdater = $conditionalFeeUpdater;
    }

    /**
     * {@inheritdoc}
     */
    public function save(QaContext $qaContext, array $postData)
    {
        $applicationStepEntity = $qaContext->getApplicationStepEntity();
        $irhpApplicationEntity = $qaContext->getQaEntity();

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

        $oldTotal = $irhpPermitApplication->getTotalEmissionsCategoryPermitsRequired();
        $irhpPermitApplication->updateEmissionsCategoryPermitsRequired($requiredEuro5, $requiredEuro6);
        $this->irhpPermitApplicationRepo->save($irhpPermitApplication);

        $this->conditionalFeeUpdater->updateFees($irhpApplicationEntity, $oldTotal);
    }
}
