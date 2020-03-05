<?php

namespace Dvsa\Olcs\Api\Service\Qa;

use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Domain\Repository\ApplicationStep as ApplicationStepRepository;

class QaContextGenerator
{
    const ERR_ALREADY_SUBMITTED = 'This application has been submitted and cannot be edited';
    const ERR_NOT_ACCESSIBLE = 'This question isn\'t yet accessible';
    const ERR_QA_NOT_SUPPORTED = 'Entity does not support q&a';

    /** @var ApplicationStepRepository */
    private $applicationStepRepo;

    /** @var QaEntityProvider */
    private $qaEntityProvider;

    /** @var QaContextFactory */
    private $qaContextFactory;

    /**
     * Create service instance
     *
     * @param ApplicationStepRepository $applicationStepRepo
     * @param QaEntityProvider $qaEntityProvider
     * @param QaContextFactory $qaContextFactory
     *
     * @return QaContextGenerator
     */
    public function __construct(
        ApplicationStepRepository $applicationStepRepo,
        QaEntityProvider $qaEntityProvider,
        QaContextFactory $qaContextFactory
    ) {
        $this->applicationStepRepo = $applicationStepRepo;
        $this->qaEntityProvider = $qaEntityProvider;
        $this->qaContextFactory = $qaContextFactory;
    }

    /**
     * Verify that the page corresponding to the specified entity ids and slug is accessible, and return a QaContext
     * instance if so
     *
     * @param int $irhpApplicationId
     * @param int|null $irhpPermitApplicationId
     * @param string $slug
     *
     * @return QaContext
     *
     * @throws ForbiddenException if the application or application step is not accessible
     */
    public function generate($irhpApplicationId, $irhpPermitApplicationId, $slug)
    {
        $qaEntity = $this->qaEntityProvider->get($irhpApplicationId, $irhpPermitApplicationId);

        if (!$qaEntity->isApplicationPathEnabled()) {
            throw new ForbiddenException(self::ERR_QA_NOT_SUPPORTED);
        }

        if (!$qaEntity->isNotYetSubmitted()) {
            throw new ForbiddenException(self::ERR_ALREADY_SUBMITTED);
        }

        $applicationPath = $qaEntity->getActiveApplicationPath();

        $applicationStep = $this->applicationStepRepo->fetchByApplicationPathIdAndSlug(
            $applicationPath->getId(),
            $slug
        );

        try {
            $previousApplicationStep = $applicationStep->getPreviousApplicationStep();
        } catch (NotFoundException $e) {
            $previousApplicationStep = null;
        }

        if (is_object($previousApplicationStep) && is_null($qaEntity->getAnswer($previousApplicationStep))) {
            throw new ForbiddenException(self::ERR_NOT_ACCESSIBLE);
        }

        return $this->qaContextFactory->create($applicationStep, $qaEntity);
    }
}
