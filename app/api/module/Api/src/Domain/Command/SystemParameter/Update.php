<?php

namespace Dvsa\Olcs\Api\Domain\Command\SystemParameter;

use Dvsa\Olcs\Api\Domain\Command\AbstractIdOnlyCommand;

/**
 * Update a SystemParameter
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
final class Update extends AbstractIdOnlyCommand
{
    protected $value;

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}
