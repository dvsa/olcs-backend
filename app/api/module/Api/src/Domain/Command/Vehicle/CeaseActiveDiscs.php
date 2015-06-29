<?php

/**
 * Cease Active Discs
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Command\Vehicle;

use Dvsa\Olcs\Transfer\Util\Annotation as Transfer;
use Dvsa\Olcs\Transfer\Command\AbstractCommand;

/**
 * Cease Active Discs
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class CeaseActiveDiscs extends AbstractCommand
{
    /**
     * @Transfer\ArrayInput
     * @Transfer\Filter({"name":"Zend\Filter\Digits"})
     * @Transfer\Validator({"name":"Zend\Validator\Digits"})
     * @Transfer\Validator({"name":"Zend\Validator\GreaterThan", "options": {"min": 0}})
     */
    protected $ids;

    /**
     * @return mixed
     */
    public function getIds()
    {
        return $this->ids;
    }
}
