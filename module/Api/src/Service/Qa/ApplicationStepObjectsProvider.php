<?php

namespace Dvsa\Olcs\Api\Service\Qa;

use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Domain\Repository\ApplicationStep as ApplicationStepRepository;
use Dvsa\Olcs\Api\Domain\Repository\IrhpApplication as IrhpApplicationRepository;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;

class ApplicationStepObjectsProvider
{
    const ERR_ALREADY_SUBMITTED = 'This application has been submitted and cannot be edited';
    const ERR_NOT_ACCESSIBLE = 'This question isn\'t yet accessible';

    /** @var ApplicationStepRepository */
    private $applicationStepRepo;

    /** @var IrhpApplicationRepository */
    private $irhpApplicationRepo;

    /**
     * Create service instance
     *
     * @param ApplicationStepRepository $applicationStepRepo
     * @param IrhpApplicationRepository $irhpApplicationRepo
     *
     * @return ApplicationStepObjectsProvider
     */
    public function __construct(
        ApplicationStepRepository $applicationStepRepo,
        IrhpApplicationRepository $irhpApplicationRepo
    ) {
        $this->applicationStepRepo = $applicationStepRepo;
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
        /** @var IrhpApplication $irhpApplication */
        $irhpApplication = $this->irhpApplicationRepo->fetchById($irhpApplicationId);
        if (!$irhpApplication->isNotYetSubmitted()) {
            throw new ForbiddenException(self::ERR_ALREADY_SUBMITTED);
        }

        $applicationPath = $irhpApplication->getActiveApplicationPath();

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
