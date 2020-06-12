<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\EcmtShortTerm;

use Dvsa\Olcs\Api\Domain\Repository\IrhpApplication as IrhpApplicationRepository;
use Dvsa\Olcs\Api\Service\Qa\QaContext;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\AnswerClearerInterface;
use Dvsa\Olcs\Api\Service\Qa\Supports\IrhpApplicationOnlyTrait;

class IntJourneysAnswerClearer implements AnswerClearerInterface
{
    use IrhpApplicationOnlyTrait;

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
    public function clear(QaContext $qaContext)
    {
        $irhpApplicationEntity = $qaContext->getQaEntity();

        $irhpApplicationEntity->clearInternationalJourneys();
        $this->irhpApplicationRepo->save($irhpApplicationEntity);
    }
}
