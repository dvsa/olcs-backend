<?php

/**
 * Generate SlaTargetDate
 */
namespace Dvsa\Olcs\Api\Domain\Command\System;

use Dvsa\Olcs\Transfer\Command\AbstractCommand;

/**
 * Generate SlaTargetDate
 */
final class GenerateSlaTargetDate extends AbstractCommand
{
    /**
     * @var int
     */
    protected $pi;

    /**
     * @return int
     */
    public function getPi()
    {
        return $this->pi;
    }
}
