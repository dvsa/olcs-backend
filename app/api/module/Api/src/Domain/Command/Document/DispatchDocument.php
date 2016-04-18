<?php

/**
 * Dispatch Document
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Command\Document;

use Dvsa\Olcs\Transfer\Command\AbstractCommand;

/**
 * Dispatch Document
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class DispatchDocument extends CreateDocumentSpecific
{
    protected $user;

    /**
     * Get user id
     *
     * @return int
     */
    public function getUser()
    {
        return $this->user;
    }
}
