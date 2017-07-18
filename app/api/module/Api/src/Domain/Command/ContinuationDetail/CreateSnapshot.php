<?php

namespace Dvsa\Olcs\Api\Domain\Command\ContinuationDetail;

use Dvsa\Olcs\Api\Domain\Command\AbstractIdOnlyCommand;

/**
 * Create continuation detail snapshot
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
final class CreateSnapshot extends AbstractIdOnlyCommand
{
    protected $user;

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }
}
