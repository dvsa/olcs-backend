<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\EcmtShortTerm;

use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitApplication as IrhpPermitApplicationRepository;
use Dvsa\Olcs\Api\Service\Qa\QaContext;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\AnswerClearerInterface;
use Dvsa\Olcs\Api\Service\Qa\Supports\IrhpApplicationOnlyTrait;

class NoOfPermitsAnswerClearer implements AnswerClearerInterface
{
    use IrhpApplicationOnlyTrait;

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
    public function clear(QaContext $qaContext)
    {
        $irhpPermitApplication = $qaContext->getQaEntity()->getFirstIrhpPermitApplication();
        $irhpPermitApplication->clearEmissionsCategoryPermitsRequired();
        $this->irhpPermitApplicationRepo->save($irhpPermitApplication);
    }
}
