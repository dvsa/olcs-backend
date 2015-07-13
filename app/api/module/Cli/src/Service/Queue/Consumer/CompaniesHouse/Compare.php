<?php

/**
 * Companies House Compare Queue Consumer
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Cli\Service\Queue\Consumer\CompaniesHouse;

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
    protected $command = 'CompaniesHouseCompare';
}
