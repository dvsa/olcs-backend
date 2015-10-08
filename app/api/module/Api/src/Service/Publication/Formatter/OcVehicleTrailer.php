<?php

namespace Dvsa\Olcs\Api\Service\Publication\Formatter;

/**
 * OcVehicleTrailer
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class OcVehicleTrailer
{
    public static function format($aoc)
    {
        $text = [];
        if ((int) $aoc->getNoOfVehiclesRequired() > 0) {
            $text[] = $aoc->getNoOfVehiclesRequired() .' vehicle(s)';
        }
        if ((int) $aoc->getNoOfTrailersRequired() > 0) {
            $text[] = $aoc->getNoOfTrailersRequired() .' trailer(s)';
        }

        return implode(', ', $text);
    }
}
