<?php

namespace Dvsa\Olcs\Api\Service\Publication\Context;

interface ContextInterface
{
    public function provide(\ArrayObject $context);
}