<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Auth\Service\OpenAm\Callback;

class PasswordCallback extends AbstractTextPromptCallback
{
    /**
     * @var string
     */
    protected $type = 'PasswordCallback';
}
