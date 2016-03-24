<?php

/**
 * Create a SystemParameter
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\SystemParameter;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\System\SystemParameter;

/**
 * Create a SystemParameter
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
final class Create extends AbstractCommandHandler
{
    protected $repoServiceName = 'SystemParameter';

    /**
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        $systemParameter = new SystemParameter();
        $systemParameter->setId($command->getId());
        $systemParameter->setParamValue($command->getParamValue());
        $systemParameter->setDescription($command->getDescription());
        $this->getRepo()->save($systemParameter);

        $this->result->addId('systemParameter', $systemParameter->getId());
        $this->result->addMessage("System Parameter '{$systemParameter->getId()}' created");
        return $this->result;
    }
}
