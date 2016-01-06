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
 */
final class Delete extends AbstractDeleteCommandHandler
{
    protected $repoServiceName = 'SiPenalty';
}
