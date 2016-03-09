<?php

/**
 * Delete SiPenalty ("applied penalty" on the internal side)
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Cases\Si\Applied;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractDeleteCommandHandler;
use Dvsa\Olcs\Api\Entity as Entities;
use Doctrine\ORM\Query;

/**
 * Delete SiPenalty ("applied penalty" on the internal side)
 * @to-do we should stop a penalty from being deletable based on criteria such as the msi response already being sent,
 * case being closed etc.
 */
final class Delete extends AbstractDeleteCommandHandler
{
    protected $repoServiceName = 'SiPenalty';
}
