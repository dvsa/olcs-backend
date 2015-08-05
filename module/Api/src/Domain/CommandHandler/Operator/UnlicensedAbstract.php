<?php

/**
* Abstract Unlicensed Operator Command Handler
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Operator;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;

/**
 * Abstract Unlicensed Operator Command Handler
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
abstract class UnlicensedAbstract extends AbstractCommandHandler
{
    const LICENCE_NUMBER_PREFIX = 'U';

    /**
     * Get a licence number for an unlicensed operator
     *
     * @param string $categoryPrefix e.g. 'O'
     * @param string $trafficAreaId e.g. 'B'
     * @param string $number e.g. '1234567'
     * @return string e.g. 'UOB1234567'
     */
    protected function buildLicenceNumber($categoryPrefix, $trafficAreaId, $number)
    {
        return sprintf(
            '%s%s%s%s',
            self::LICENCE_NUMBER_PREFIX,
            $categoryPrefix,
            $trafficAreaId,
            $number
        );
    }
}
