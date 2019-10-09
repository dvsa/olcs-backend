<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\EcmtShortTerm;

use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Text\Text;

class AnnualTripsAbroadFactory
{
    /**
     * Create and return a AnnualTripsAbroad instance
     *
     * @param int $intensityWarningThreshold
     * @param bool $showNiWarning
     * @param Text $text
     *
     * @return AnnualTripsAbroad
     */
    public function create($intensityWarningThreshold, $showNiWarning, Text $text)
    {
        return new AnnualTripsAbroad($intensityWarningThreshold, $showNiWarning, $text);
    }
}
