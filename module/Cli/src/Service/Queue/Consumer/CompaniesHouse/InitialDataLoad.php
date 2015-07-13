<?php

/**
 * Companies House Initial Data Load Queue Consumer
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Cli\Service\Queue\Consumer\CompaniesHouse;

/**
 * Companies House Initial Data Load Queue Consumer
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class InitialDataLoad extends AbstractConsumer
{
    /**
     * @var string the command to handle processing
     */
    protected $command = 'CompaniesHouseLoad';
}
