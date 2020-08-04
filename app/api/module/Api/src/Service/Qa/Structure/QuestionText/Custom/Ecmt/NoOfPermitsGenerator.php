<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText\Custom\Ecmt;

use Dvsa\Olcs\Api\Domain\Repository\FeeType as FeeTypeRepository;
use Dvsa\Olcs\Api\Service\Qa\QaContext;
use Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText\QuestionTextGenerator;
use Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText\QuestionTextGeneratorInterface;
use Dvsa\Olcs\Api\Service\Qa\Supports\IrhpApplicationOnlyTrait;

class NoOfPermitsGenerator implements QuestionTextGeneratorInterface
{
    use IrhpApplicationOnlyTrait;

    /** @var QuestionTextGenerator */
    private $questionTextGenerator;

    /** @var FeeTypeRepository */
    private $feeTypeRepo;

    /**
     * Create service instance
     *
     * @param QuestionTextGenerator $questionTextGenerator
     * @param FeeTypeRepository $feeTypeRepo
     *
     * @return NoOfPermitsGenerator
     */
    public function __construct(
        QuestionTextGenerator $questionTextGenerator,
        FeeTypeRepository $feeTypeRepo
    ) {
        $this->questionTextGenerator = $questionTextGenerator;
        $this->feeTypeRepo = $feeTypeRepo;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(QaContext $qaContext)
    {
        $irhpApplicationEntity = $qaContext->getQaEntity();

        $applicationFee = $this->feeTypeRepo->getLatestByProductReference(
            $irhpApplicationEntity->getApplicationFeeProductReference()
        );

        $issueFee = $this->feeTypeRepo->getLatestByProductReference(
            $irhpApplicationEntity->getIssueFeeProductReference()
        );

        $questionText = $this->questionTextGenerator->generate($qaContext);
        $additionalGuidanceTranslateableText = $questionText->getAdditionalGuidance()->getTranslateableText();

        $additionalGuidanceTranslateableText->getParameter(0)->setValue($applicationFee->getFixedValue());
        $additionalGuidanceTranslateableText->getParameter(1)->setValue($issueFee->getFixedValue());

        return $questionText;
    }
}
