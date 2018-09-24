<?php

/**
 * Calculate randomised app score
 *
 */
namespace Dvsa\Olcs\Api\Domain\Command\Permits;

use Dvsa\Olcs\Transfer\FieldType\Traits\Identity;
use Dvsa\Olcs\Transfer\Util\Annotation as Transfer;
use Dvsa\Olcs\Transfer\Command\AbstractCommand;

/**
 */
final class CalculateRandomApplicationScore extends AbstractCommand
{
    use Identity;
}
