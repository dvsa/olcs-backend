<?php

namespace Dvsa\Olcs\Api\Service\Ebsr\Filter\Format;

use Zend\Filter\AbstractFilter;

/**
 * Class MiscSnJustification
 * @package Dvsa\Olcs\Api\Service\Ebsr\Filter\Format
 */
class MiscSnJustification extends AbstractFilter
{
    const MISC_JUSTIFICATION = 'Miscellaneous justification: %s';

    /**
     * Appends the miscellaneous justification field to the unforseen detail field
     *
     * @param  mixed $value
     * @return mixed
     */
    public function filter($value)
    {
        //if the field isn't set, we don't need to do anything
        if (!isset($value['busShortNotice']['miscJustification'])) {
            return $value;
        }

        $miscJustification = sprintf(self::MISC_JUSTIFICATION, $value['busShortNotice']['miscJustification']);

        if (!isset($value['busShortNotice']['unforseenDetail'])) {
            $value['busShortNotice']['unforseenDetail'] = $miscJustification;
        } else {
            $value['busShortNotice']['unforseenDetail'] .= ' ' . $miscJustification;
        }

        return $value;
    }
}
