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
}
