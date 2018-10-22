<?php

/**
 * Generate IRHP Permit
 *
 * @author Henry White <henry.white@capgemini.com>
 */
namespace Dvsa\Olcs\Api\Domain\Command\Permits;

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
     * @return array
     */
    public function getIds()
    {
        return $this->ids;
    }
}
