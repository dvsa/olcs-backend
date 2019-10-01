<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\EcmtShortTerm;

use Dvsa\Olcs\Api\Domain\Repository\IrhpApplication as IrhpApplicationRepository;
use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep as ApplicationStepEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use Dvsa\Olcs\Api\Service\Qa\Common\ArrayCollectionFactory;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\GenericAnswerClearer;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\AnswerClearerInterface;

class RestrictedCountriesAnswerClearer implements AnswerClearerInterface
{
    /**
     * Create service instance
     *
     * @param GenericAnswerClearer $genericAnswerClearer
     * @param IrhpApplicationRepository $irhpApplicationRepo
     * @param ArrayCollectionFactory $arrayCollectionFactory
     *
     * @return RestrictedCountriesAnswerClearer
     */
    public function __construct(
        GenericAnswerClearer $genericAnswerClearer,
        IrhpApplicationRepository $irhpApplicationRepo,
        ArrayCollectionFactory $arrayCollectionFactory
    ) {
        $this->genericAnswerClearer = $genericAnswerClearer;
        $this->irhpApplicationRepo = $irhpApplicationRepo;
        $this->arrayCollectionFactory = $arrayCollectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function clear(ApplicationStepEntity $applicationStepEntity, IrhpApplicationEntity $irhpApplicationEntity)
    {
        $this->genericAnswerClearer->clear($applicationStepEntity, $irhpApplicationEntity);

        $irhpApplicationEntity->updateCountries(
            $this->arrayCollectionFactory->create()
        );

        $this->irhpApplicationRepo->save($irhpApplicationEntity);
    }
}
