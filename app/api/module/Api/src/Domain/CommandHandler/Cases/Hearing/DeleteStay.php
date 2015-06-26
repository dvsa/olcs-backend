<?php

/**
 * Delete Stay
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Cases\Hearing;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractDeleteCommandHandler;
use Dvsa\Olcs\Api\Entity as Entities;
use Doctrine\ORM\Query;

/**
 * Delete Stay
 */
final class DeleteStay extends AbstractDeleteCommandHandler
{
    protected $repoServiceName = 'Stay';
}
