<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Auth\Service\OpenAm\Callback;

interface CallbackInterface
{
    /**
     * To array
     *
     * @return array
     */
    public function toArray(): array;
}
