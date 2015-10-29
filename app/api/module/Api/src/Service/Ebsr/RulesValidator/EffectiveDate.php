<?php

namespace Dvsa\Olcs\Api\Service\Ebsr\RulesValidator;

use Zend\Validator\AbstractValidator;
use Zend\Validator\Exception;

/**
 * Class EffectiveDate
 * @package Dvsa\Olcs\Api\Service\Ebsr\RulesValidator
 */
class EffectiveDate extends AbstractValidator
{
    const RULES_ERROR = 'rules-error';

    /**
     * @var array
     */
    protected $messageTemplates = [
        self::RULES_ERROR => 'Effective date must be in the future.'
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
     * @throws Exception\RuntimeException If validation of $value is impossible
     */
    public function isValid($value)
    {
        if ($value['txcAppType'] == 'new') {
            $date = strtotime($value['effectiveDate']);
            $today = strtotime(date('Y-m-d'));

            if ($date < $today) {
                $this->error(self::RULES_ERROR);
                return false;
            }
        }
        return true;

    }
}
