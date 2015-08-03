<?php

/**
 * Delete a Transport Manager Application for TM Responsibilities
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\TransportManagerApplication;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractDeleteCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;

/**
 * Delete a Transport Manager Application for TM Responsibilities
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
final class DeleteForResponsibilities extends AbstractDeleteCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'TransportManagerApplication';
}
