<?php

/**
 * Case Provider Interface
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\Olcs\Api\Entity;

use Dvsa\Olcs\Api\Entity\Cases\Cases;

/**
 * Case Provider Interface
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
interface CaseProviderInterface
{
    /**
     * @return Cases
     */
    public function getCase();
}
