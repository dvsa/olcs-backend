<?php

/**
 * Companies House Compare Queue Consumer
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Cli\Service\Queue\Consumer\CompaniesHouse;

use Dvsa\Olcs\Api\Domain\Command\CompaniesHouse\Compare as Cmd;

/**
 * Companies House Compare Queue Consumer
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class Compare extends AbstractConsumer
{
    /**
     * @var string the command to handle processing
     */
    protected $commandName = Cmd::class;
}
