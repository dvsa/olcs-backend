<?php

namespace Dvsa\Olcs\Api\Service\Qa;

use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Domain\Repository\ApplicationPath as ApplicationPathRepository;
use Dvsa\Olcs\Api\Domain\Repository\ApplicationStep as ApplicationStepRepository;
use Dvsa\Olcs\Api\Domain\Repository\IrhpApplication as IrhpApplicationRepository;

class ApplicationStepObjectsProvider
{
    const ERR_ALREADY_SUBMITTED = 'This application has been submitted and cannot be edited';
    const ERR_NOT_ACCESSIBLE = 'This question isn\'t yet accessible';

    /** @var ApplicationStepRepository */
    private $applicationStepRepo;

    /** @var ApplicationPathRepository */
    private $applicationPathRepo;

    /** @var IrhpApplicationRepository */
    private $irhpApplicationRepo;

    /**
     * Create service instance
     *
     * @param ApplicationStepRepository $applicationStepRepo
     * @param ApplicationPathRepository $applicationPathRepo
     * @param IrhpApplicationRepository $irhpApplicationRepo
     *
     * @return ApplicationStepObjectsProvider
     */
    public function __construct(
        ApplicationStepRepository $applicationStepRepo,
        ApplicationPathRepository $applicationPathRepo,
        IrhpApplicationRepository $irhpApplicationRepo
    ) {
        $this->applicationStepRepo = $applicationStepRepo;
        $this->applicationPathRepo = $applicationPathRepo;
        $this->irhpApplicationRepo = $irhpApplicationRepo;
    }

    /**
     * Verify that the page corresponding to the specified irhpApplicationId and slug is accessible, and return a
     * series of associated object instances if so
     *
     * @param int $irhpApplicationId
     * @param string $slug
     *
     * @return array
     *
     * @throws ForbiddenException if the application or application step is not accessible
     */
    public function getObjects($irhpApplicationId, $slug)
    {
        $irhpApplication = $this->irhpApplicationRepo->fetchById($irhpApplicationId);
        if (!$irhpApplication->isNotYetSubmitted()) {
            throw new ForbiddenException(self::ERR_ALREADY_SUBMITTED);
        }

        $applicationPath = $this->applicationPathRepo->fetchByIrhpPermitTypeIdAndDate(
            $irhpApplication->getIrhpPermitType()->getId(),
            $irhpApplication->getCreatedOn(true)
        );

        $applicationStep = $this->applicationStepRepo->fetchByApplicationPathIdAndSlug(
            $applicationPath->getId(),
            $slug
        );

        try {
            $previousApplicationStep = $applicationStep->getPreviousApplicationStep();
        } catch (NotFoundException $e) {
            $previousApplicationStep = null;
        }

        if (is_object($previousApplicationStep) && is_null($irhpApplication->getAnswer($previousApplicationStep))) {
            throw new ForbiddenException(self::ERR_NOT_ACCESSIBLE);
        }

        return [
            'applicationStep' => $applicationStep,
            'irhpApplication' => $irhpApplication,
        ];
    }
}
