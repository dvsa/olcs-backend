<?php

/**
 * Companies House InitialLoad command test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\OlcsTest\Api\Domain\Command\CompaniesHouse;

use Dvsa\Olcs\Api\Domain\Command\CompaniesHouse\InitialLoad;
use PHPUnit_Framework_TestCase;

/**
 * Companies House InitialLoad command test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class InitialLoadTest extends PHPUnit_Framework_TestCase
{
    public function testStructure()
    {
        $command = InitialLoad::create(['companyNumber' => '01234567']);

        $this->assertEquals('01234567', $command->getCompanyNumber());
        $this->assertEquals(['companyNumber' => '01234567'], $command->getArrayCopy());
    }
}
