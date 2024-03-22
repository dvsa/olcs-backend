<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Api\Domain;

use Laminas\Http\Request;

interface RequestAwareInterface
{
    public function setRequest(Request $request): void;
    public function getRequest(): Request;
}
