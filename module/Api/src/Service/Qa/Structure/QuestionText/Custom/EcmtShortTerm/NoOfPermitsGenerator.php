<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText\Custom\EcmtShortTerm;

use Dvsa\Olcs\Api\Entity\Fee\FeeType as FeeTypeEntity;
use Dvsa\Olcs\Api\Domain\Repository\FeeType as FeeTypeRepository;
use Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText\QuestionTextGenerator;
use Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText\QuestionTextGeneratorInterface;
use Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText\QuestionTextGeneratorContext;

class NoOfPermitsGenerator implements QuestionTextGeneratorInterface
{
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
    public function generate(QuestionTextGeneratorContext $context)
    {
        $feePerPermit = $context->getIrhpApplicationEntity()->getFeePerPermit(
            $this->feeTypeRepo->getLatestByProductReference(FeeTypeEntity::FEE_TYPE_ECMT_APP_PRODUCT_REF),
            $this->feeTypeRepo->getLatestByProductReference(FeeTypeEntity::FEE_TYPE_ECMT_SHORT_TERM_ISSUE_PRODUCT_REF)
        );

        $questionText = $this->questionTextGenerator->generate($context);
        $questionText->getGuidance()->getTranslateableText()->getParameter(0)->setValue($feePerPermit);

        return $questionText;
    }
}
