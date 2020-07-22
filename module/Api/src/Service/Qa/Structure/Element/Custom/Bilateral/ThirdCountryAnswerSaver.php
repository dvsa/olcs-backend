<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Bilateral;

use Dvsa\Olcs\Api\Service\Qa\QaContext;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\AnswerSaverInterface;
use Dvsa\Olcs\Api\Service\Qa\Supports\IrhpPermitApplicationOnlyTrait;

class ThirdCountryAnswerSaver implements AnswerSaverInterface
{
    const YES_ANSWER = 'qanda.bilaterals.third-country.yes-answer';

    use IrhpPermitApplicationOnlyTrait;

    /** @var CountryDeletingAnswerSaver */
    private $countryDeletingAnswerSaver;

    /**
     * Create service instance
     *
     * @param CountryDeletingAnswerSaver $countryDeletingAnswerSaver
     *
     * @return ThirdCountryAnswerSaver
     */
    public function __construct(CountryDeletingAnswerSaver $countryDeletingAnswerSaver)
    {
        $this->countryDeletingAnswerSaver = $countryDeletingAnswerSaver;
    }

    /**
     * {@inheritdoc}
     */
    public function save(QaContext $qaContext, array $postData)
    {
        return $this->countryDeletingAnswerSaver->save(
            $qaContext,
            $postData,
            self::YES_ANSWER
        );
    }
}
