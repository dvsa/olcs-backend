<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Ecmt;

use Dvsa\Olcs\Api\Domain\Repository\IrhpApplication as IrhpApplicationRepository;
use Dvsa\Olcs\Api\Service\Qa\QaContext;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\AnswerClearerInterface;
use Dvsa\Olcs\Api\Service\Qa\Supports\IrhpApplicationOnlyTrait;

class SectorsAnswerClearer implements AnswerClearerInterface
{
    use IrhpApplicationOnlyTrait;

    /** @var IrhpApplicationRepository */
    private $irhpApplicationRepo;

    /**
     * Create service instance
     *
     * @param IrhpApplicationRepository $irhpApplicationRepo
     *
     * @return SectorsAnswerClearer
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

        $irhpApplicationEntity->clearSectors();
        $this->irhpApplicationRepo->save($irhpApplicationEntity);
    }
}
