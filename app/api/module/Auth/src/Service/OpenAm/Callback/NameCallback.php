<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Auth\Service\OpenAm\Callback;

class NameCallback extends AbstractTextPromptCallback
{
    /**
     * @var string
     */
    protected $type = 'NameCallback';
}
