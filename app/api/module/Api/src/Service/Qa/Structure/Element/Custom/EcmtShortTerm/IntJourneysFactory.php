<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\EcmtShortTerm;

use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Radio\Radio;

class IntJourneysFactory
{
    /**
     * Create and return a IntJourneys instance
     *
     * @param bool $showNiWarning
     * @param Radio $radio
     *
     * @return IntJourneys
     */
    public function create($showNiWarning, Radio $radio)
    {
        return new IntJourneys($showNiWarning, $radio);
    }
}
