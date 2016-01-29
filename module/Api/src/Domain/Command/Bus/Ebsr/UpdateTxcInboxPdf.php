<?php

/**
 * Update Txc Inbox records for a bus reg id with TransXchange PDF
 */
namespace Dvsa\Olcs\Api\Domain\Command\Bus\Ebsr;

use Dvsa\Olcs\Api\Domain\Command\AbstractIdOnlyCommand;

/**
 * Update Txc Inbox records for a bus reg id with TransXchange PDF
 */
final class UpdateTxcInboxPdf extends AbstractIdOnlyCommand
{
    protected $document;

    /**
     * @return int
     */
    public function getDocument()
    {
        return $this->document;
    }
}
