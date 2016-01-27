<?php

namespace Dvsa\Olcs\Api\Service\Ebsr\RulesValidator\ProcessedData;

use Zend\Validator\AbstractValidator;
use Zend\Validator\Exception;
use Dvsa\Olcs\Api\Entity\Bus\BusReg as BusRegEntity;

/**
 * Class RegisteredBusRoute
 * @package Dvsa\Olcs\Api\Service\Ebsr\RulesValidator\ProcessedData
 */
class RegisteredBusRoute extends AbstractValidator
{
    const REGISTERED_BUS_ROUTE_ERROR = 'registered-bus-route-error';
    const TYPE_CANCELLATION = 'cancellation';
    const TYPE_VARIATION = 'variation';

    /**
     * @var array
     */
    protected $messageTemplates = [
        self::REGISTERED_BUS_ROUTE_ERROR => 'You can only create a %type% against a registered bus route'
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
        $txcAppType = strtolower($value['txcAppType']);

        if ($txcAppType !== 'new' && $busReg instanceof BusRegEntity && !$busReg->isRegistered()) {
            $type = ($txcAppType === 'cancel' ? self::TYPE_CANCELLATION : self::TYPE_VARIATION);
            $this->error(self::REGISTERED_BUS_ROUTE_ERROR, $type);
            return false;
        }

        return true;
    }
}
