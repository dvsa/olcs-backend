<?php

namespace Dvsa\Olcs\Api\Domain\Validation\Validators;

/**
 * Can access TxcInbox
 */
class CanAccessTxcInbox extends AbstractCanAccessEntity
{
    protected $repo = 'TxcInbox';
}
