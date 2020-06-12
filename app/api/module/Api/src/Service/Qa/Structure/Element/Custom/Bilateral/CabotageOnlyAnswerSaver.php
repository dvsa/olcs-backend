<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Bilateral;

use Dvsa\Olcs\Api\Entity\Generic\Answer;
use Dvsa\Olcs\Api\Entity\Generic\Question;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Service\Permits\Bilateral\ApplicationCountryRemover;
use Dvsa\Olcs\Api\Service\Qa\AnswerSaver\GenericAnswerWriter;
use Dvsa\Olcs\Api\Service\Qa\QaContext;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\AnswerSaverInterface;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\GenericAnswerFetcher;
use Dvsa\Olcs\Api\Service\Qa\Supports\IrhpPermitApplicationOnlyTrait;

class CabotageOnlyAnswerSaver implements AnswerSaverInterface
{
    const FRONTEND_DESTINATION_OVERVIEW = 'OVERVIEW';
    const FRONTEND_DESTINATION_CANCEL = 'CANCEL';

    use IrhpPermitApplicationOnlyTrait;

    /** @var GenericAnswerFetcher */
    private $genericAnswerFetcher;

    /** @var GenericAnswerWriter */
    private $genericAnswerWriter;

    /** @var ApplicationCountryRemover */
    private $applicationCountryRemover;

    /**
     * Create service instance
     *
     * @param GenericAnswerFetcher $genericAnswerFetcher
     * @param GenericAnswerWriter $genericAnswerWriter
     * @param ApplicationCountryRemover $applicationCountryRemover
     *
     * @return CabotageOnlyAnswerSaver
     */
    public function __construct(
        GenericAnswerFetcher $genericAnswerFetcher,
        GenericAnswerWriter $genericAnswerWriter,
        ApplicationCountryRemover $applicationCountryRemover
    ) {
        $this->genericAnswerFetcher = $genericAnswerFetcher;
        $this->genericAnswerWriter = $genericAnswerWriter;
        $this->applicationCountryRemover = $applicationCountryRemover;
    }

    /**
     * {@inheritdoc}
     */
    public function save(QaContext $qaContext, array $postData)
    {
        $cabotageRequired = $this->genericAnswerFetcher->fetch(
            $qaContext->getApplicationStepEntity(),
            $postData
        );

        if ($cabotageRequired == 'Y') {
            $this->genericAnswerWriter->write(
                $qaContext,
                Answer::BILATERAL_CABOTAGE_ONLY,
                Question::QUESTION_TYPE_STRING
            );

            return;
        }

        $irhpPermitApplication = $qaContext->getQaEntity();

        $countries = $irhpPermitApplication->getIrhpApplication()->getCountrys();
        if (count($countries) > 1) {
            $this->applicationCountryRemover->remove(
                $qaContext->getQaEntity()
            );

            return self::FRONTEND_DESTINATION_OVERVIEW;
        }

        return self::FRONTEND_DESTINATION_CANCEL;
    }
}
