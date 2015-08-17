<?php

/**
 * Print goods discs
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\GoodsDisc;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;

/**
 * Print goods discs
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
final class PrintDiscs extends AbstractCommandHandler
{
    protected $repoServiceName = 'GoodsDisc';

    public function handleCommand(CommandInterface $command)
    {
        $discsToPrint = $this->getRepo()->fetchDiscsToPrint
            ($command->getNiFlag(), $command->getOperatorType(), $command->getLicenceType()
        );

        //return $result;
    }
}
