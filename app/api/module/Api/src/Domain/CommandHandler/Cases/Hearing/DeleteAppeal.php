<?php

/**
 * Delete Appeal
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Cases\Hearing;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractDeleteCommandHandler;
use Dvsa\Olcs\Api\Entity as Entities;
use Doctrine\ORM\Query;

/**
 * Delete Appeal
 */
final class DeleteAppeal extends AbstractDeleteCommandHandler
{
    protected $repoServiceName = 'Appeal';
}
