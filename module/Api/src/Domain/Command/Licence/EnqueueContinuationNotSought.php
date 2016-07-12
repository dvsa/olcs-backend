<?php

namespace Dvsa\Olcs\Api\Domain\Command\Licence;

use Dvsa\Olcs\Transfer\Command\AbstractCommand;

/**
 * Enqueue CNS jobs
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
final class EnqueueContinuationNotSought extends AbstractCommand
{
    protected $licences;

    protected $date;

    /**
     * Get licences
     *
     * @return mixed
     */
    public function getLicences()
    {
        return $this->licences;
    }

    /**
     * Get date
     *
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }
}
