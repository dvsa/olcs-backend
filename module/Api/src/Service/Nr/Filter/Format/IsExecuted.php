<?php

namespace Dvsa\Olcs\Api\Service\Nr\Filter\Format;

use Laminas\Filter\AbstractFilter as LaminasAbstractFilter;

/**
 * Class IsExecuted
 * @package Dvsa\Olcs\Api\Service\Nr\Filter\Format
 */
class IsExecuted extends LaminasAbstractFilter
{
    const YES_EXECUTED_KEY = 'pen_erru_imposed_executed_yes';
    const NO_EXECUTED_KEY = 'pen_erru_imposed_executed_no';
    const UNKNOWN_EXECUTED_KEY = 'pen_erru_imposed_executed_un';

    /**
     * Returns the result of filtering $value
     *
     * @param  array $value
     * @return array
     */
    public function filter($value)
    {
        if (empty($value['imposedErrus'])) {
            return $value;
        }

        foreach ($value['imposedErrus'] as $key => $erru) {
            //lowercase the value to make sure we always get a match
            $executed = strtolower($erru['executed']);

            switch ($executed) {
                case 'yes':
                    $newValue = self::YES_EXECUTED_KEY;
                    break;
                case 'no':
                    $newValue = self::NO_EXECUTED_KEY;
                    break;
                default:
                    $newValue = self::UNKNOWN_EXECUTED_KEY;
            }

            $value['imposedErrus'][$key]['executed'] = $newValue;
        }

        return $value;
    }
}
