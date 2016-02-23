<?php

namespace Dvsa\Olcs\Api\Service\Ebsr\RulesValidator;

use Zend\Validator\AbstractValidator;
use Zend\Validator\Exception;

/**
 * Class ApplicationType
 * @package Dvsa\Olcs\Api\Service\Ebsr\RulesValidator
 */
class ApplicationType extends AbstractValidator
{
    const RULES_ERROR = 'app-type-error';

    /**
     * @var array
     */
    protected $messageTemplates = [
        self::RULES_ERROR => 'Application type for a data refresh must be Non chargeable change'
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
    public function isValid($value, $context = [])
    {
        if ($context['submissionType'] == 'ebsrt_refresh') {
            if ($value['txcAppType'] != 'nonChargeableChange') {
                $this->error(self::RULES_ERROR);
                return false;
            }
        }

        return true;
    }
}
