<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\SystemParameter;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;

/**
 * Update a SystemParameter
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
final class Update extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'SystemParameter';

    /**
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        /* @var $systemParameter \Dvsa\Olcs\Api\Entity\System\SystemParameter */
        $systemParameter = $this->getRepo()->fetchUsingId($command);
        $systemParameter->setParamValue($command->getParamValue());
        if (method_exists($command, 'getDescription')) {
            $systemParameter->setDescription($command->getDescription());
        }
        $this->getRepo()->save($systemParameter);

        $this->result->addMessage("SystemParameter '{$command->getId()}' updated");
        return $this->result;
    }
}
