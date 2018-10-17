<?php

/**
 * Generate IRHP Permit
 *
 * @author Henry White <henry.white@capgemini.com>
 */
namespace Dvsa\Olcs\Transfer\Command\Permits;

use Dvsa\Olcs\Transfer\Command\AbstractCommand;

/**
 * GeneratePermit
 *
 * @author Henry White <henry.white@capgemini.com>
 */
final class GeneratePermit extends AbstractCommand
{
    protected $ids = [];

    /**
     * @return mixed
     */
    public function getIds()
    {
        return $this->ids;
    }
}
