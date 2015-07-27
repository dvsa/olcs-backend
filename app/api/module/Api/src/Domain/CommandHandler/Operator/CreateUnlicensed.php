<?php

/**
 * Create Unlicensed Operator
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Operator;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Create Unlicensed Operator
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
final class CreateUnlicensed extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Organisation';

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        return $result;
    }
}
