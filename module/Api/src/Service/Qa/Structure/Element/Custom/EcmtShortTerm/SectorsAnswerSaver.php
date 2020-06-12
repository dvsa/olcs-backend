<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\EcmtShortTerm;

use Dvsa\Olcs\Api\Domain\Repository\IrhpApplication as IrhpApplicationRepository;
use Dvsa\Olcs\Api\Entity\Permits\Sectors as SectorsEntity;
use Dvsa\Olcs\Api\Service\Qa\QaContext;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\AnswerSaverInterface;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\GenericAnswerFetcher;
use Dvsa\Olcs\Api\Service\Qa\Supports\IrhpApplicationOnlyTrait;

class SectorsAnswerSaver implements AnswerSaverInterface
{
    use IrhpApplicationOnlyTrait;

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
     * @return SectorsAnswerSaver
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
    public function save(QaContext $qaContext, array $postData)
    {
        $answer = $this->genericAnswerFetcher->fetch(
            $qaContext->getApplicationStepEntity(),
            $postData
        );

        $irhpApplicationEntity = $qaContext->getQaEntity();

        $irhpApplicationEntity->updateSectors(
            $this->irhpApplicationRepo->getReference(SectorsEntity::class, $answer)
        );

        $this->irhpApplicationRepo->save($irhpApplicationEntity);
    }
}
