<?php

namespace Dvsa\Olcs\Api\Service\Ebsr\RulesValidator\ProcessedData;

use Zend\Validator\AbstractValidator;
use Zend\Validator\Exception;
use Dvsa\Olcs\Api\Entity\Bus\BusReg as BusRegEntity;

/**
 * Class VariationNumber
 * @package Dvsa\Olcs\Api\Service\Ebsr\RulesValidator\ProcessedData
 */
class VariationNumber extends AbstractValidator
{
    const CANCELLATION_VARIATION_NUMBER_ERROR = 'cancellation-variation-number-error';
    const VARIATION_VARIATION_NUMBER_ERROR = 'variation-variation-number-error';
    const NEW_VARIATION_NUMBER_ERROR = 'new-variation-number-error';

    /**
     * @var array
     */
    protected $messageTemplates = [
        self::CANCELLATION_VARIATION_NUMBER_ERROR =>
            'For cancellations, the variation number must be equal to previous variation number',
        self::VARIATION_VARIATION_NUMBER_ERROR =>
            'For variations, the variation number should be 1 greater than the previous variation number',
        self::NEW_VARIATION_NUMBER_ERROR => 'For new applications, the variation number must be zero'
    ];

    /**
     * Returns true if and only if $value meets the validation requirements
     *
     * If $value fails validation, then this method returns false, and
     * getMessages() will return an array of messages that explain why the
     * validation failed.
     *
     * @param  mixed $value
     * @return bool
     */
    public function isValid($value, $context = [])
    {
        /** @var BusRegEntity $busReg */
        $busReg = $context['busReg'];

        if (strtolower($value['txcAppType']) === 'cancel') {
            if ($value['variationNo'] != $busReg->getVariationNo()) {
                $this->error(self::CANCELLATION_VARIATION_NUMBER_ERROR);
                return false;
            }
        } elseif (strtolower($value['txcAppType']) === 'new') {
            if ($value['variationNo'] != 0) {
                $this->error(self::NEW_VARIATION_NUMBER_ERROR);
                return false;
            }
        } else {
            if ($value['variationNo'] != $busReg->getVariationNo() + 1) {
                $this->error(self::VARIATION_VARIATION_NUMBER_ERROR);
                return false;
            }
        }

        return true;
    }
}
