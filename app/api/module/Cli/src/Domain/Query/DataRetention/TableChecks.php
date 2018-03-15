<?php

namespace Dvsa\Olcs\Cli\Domain\Query\DataRetention;

use Dvsa\Olcs\Transfer\Query\AbstractQuery;

/**
 * Table check dto
 *
 * @author Andy Newton <andy@vitri.ltd>
 */
class TableChecks extends AbstractQuery
{
    /**
     * @var bool
     */
    protected $isPostCheck;

    /**
     * Gets the value of isPostCheck.
     *
     * @return bool
     */
    public function getIsPostCheck()
    {
        return $this->isPostCheck;
    }
}
