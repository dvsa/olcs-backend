<?php

namespace Dvsa\Olcs\Api\Service\Ebsr\RulesValidator\ProcessedData;

use Zend\Validator\AbstractValidator;
use Zend\Validator\Exception;
use Dvsa\Olcs\Api\Entity\Bus\BusReg as BusRegEntity;

/**
 * Class NewAppAlreadyExists
 * @package Dvsa\Olcs\Api\Service\Ebsr\RulesValidator\ProcessedData
 */
class NewAppAlreadyExists extends AbstractValidator
{
    const NEW_APP_ALREADY_EXISTS_ERROR = 'new-app-already-exists-error';

    /**
     * @var array
     */
    protected $messageTemplates = [
        self::NEW_APP_ALREADY_EXISTS_ERROR => 'A new application can\'t reuse an existing registration number: %value%'
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
        if ($value['txcAppType'] === 'new' && $context['busReg'] instanceof BusRegEntity) {
            $this->error(self::NEW_APP_ALREADY_EXISTS_ERROR, $value['existingRegNo']);
            return false;
        }

        return true;
    }
}
