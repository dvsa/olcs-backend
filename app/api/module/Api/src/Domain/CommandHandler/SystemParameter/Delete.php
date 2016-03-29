<?php

/**
 * Delete a SystemParameter
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\SystemParameter;

use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractDeleteCommandHandler;

/**
 * Delete a SystemParameter
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
final class Delete extends AbstractDeleteCommandHandler
{
    protected $repoServiceName = 'SystemParameter';
}
