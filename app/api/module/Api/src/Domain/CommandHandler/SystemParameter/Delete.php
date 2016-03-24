<?php

/**
 * Delete a SystemParameter
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\SystemParameter;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Delete a SystemParameter
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
final class Delete extends AbstractCommandHandler
{
    protected $repoServiceName = 'SystemParameter';

    public function handleCommand(CommandInterface $command)
    {
        $systemParameter = $this->getRepo()->fetchUsingId($command);
        $this->getRepo()->delete($systemParameter);

        $this->result->addId('systemParameter', $systemParameter->getId());
        $this->result->addMessage('System Parameter deleted successfully');
        return $this->result;
    }
}
