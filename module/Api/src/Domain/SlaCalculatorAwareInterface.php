<?php

namespace Dvsa\Olcs\Api\Domain;

use Dvsa\Olcs\Api\Domain\Util\SlaCalculatorInterface;

interface SlaCalculatorAwareInterface
{
    /**
     * @param SlaCalculatorInterface $slaCalculator
     */
    public function setSlaCalculator(SlaCalculatorInterface $slaCalculator);

    /**
     * @return SlaCalculatorInterface
     */
    public function getSlaCalculator();
}
