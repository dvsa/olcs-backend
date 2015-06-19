<?php

/**
 * Delete Opposition
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Cases\Opposition;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractDeleteCommandHandler;
use Dvsa\Olcs\Api\Entity as Entities;
use Doctrine\ORM\Query;

/**
 * Delete Opposition
 */
final class DeleteOpposition extends AbstractDeleteCommandHandler
{
    protected $repoServiceName = 'Opposition';
}
