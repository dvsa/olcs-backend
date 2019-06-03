<?php

/**
 * Delete a Document Template
 *
 * @author Andy Newton <andy@vitri.ltd>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\DocTemplate;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractDeleteCommandHandler;

final class Delete extends AbstractDeleteCommandHandler
{
    protected $repoServiceName = 'DocTemplate';
}
