<?php

namespace Dvsa\Olcs\Api\Entity\System;

use Doctrine\ORM\Mapping as ORM;

/**
 * SystemParameter Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="system_parameter")
 */
class SystemParameter extends AbstractSystemParameter
{
    const CNS_EMAIL_LIST = 'CNS_EMAIL_LIST';
    const DISABLED_SELFSERVE_CARD_PAYMENTS = 'DISABLED_SELFSERVE_CARD_PAYMENTS';
    const SELFSERVE_USER_PRINTER = 'SELFSERVE_USER_PRINTER';
    const RESOLVE_CARD_PAYMENTS_MIN_AGE = 'RESOLVE_CARD_PAYMENTS_MIN_AGE';
}
