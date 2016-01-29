<?php

namespace Dvsa\Olcs\Api\Service\Ebsr\RulesValidator\ShortNotice;

use Zend\Validator\AbstractValidator;
use Zend\Validator\Exception;
use Dvsa\Olcs\Api\Entity\Bus\BusReg as BusRegEntity;

/**
 * Class MissingReason
 * @package Dvsa\Olcs\Api\Service\Ebsr\RulesValidator\ShortNotice
 */
class MissingReason extends AbstractValidator
{
    const SHORT_NOTICE_MISSING_REASON_ERROR = 'short-notice-missing-reason-error';

    /**
     * @var array
     */
    protected $messageTemplates = [
        self::SHORT_NOTICE_MISSING_REASON_ERROR => 'A short notice reason hasn\'t been provided'
    ];

    /**
     * Returns true if and only if $value meets the validation requirements
     *
     * If $value fails validation, then this method returns false, and
     * getMessages() will return an array of messages that explain why the
     * validation failed.
     *
     * @param  mixed $value
     * @param array $context
     * @return bool
     */
    public function isValid($value, $context = [])
    {
        /** @var BusRegEntity $busReg */
        $busReg = $context['busReg'];

        if ($busReg->getIsShortNotice() === 'Y') {
            $sn = $value['busShortNotice'];

            //we can only do this validation if fields have been explicitly set
            if ((isset($sn['notAvailableChange']) && isset($sn['timetableChange']))
                && ($sn['notAvailableChange'] === 'N' && $sn['timetableChange'] === 'N')) {
                //if the change is available to the public and its not a timetable (low impact) change, an additional
                //reason must be supplied, thus the busShortNoticeArray must contain at least 3 elements.
                //(not available, timetable change and the additional reason element)
                if (count($sn) < 3) {
                    $this->error(self::SHORT_NOTICE_MISSING_REASON_ERROR);
                    return false;
                }
            }
        }

        return true;
    }
}
