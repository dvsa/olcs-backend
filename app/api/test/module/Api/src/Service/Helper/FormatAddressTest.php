<?php

/**
 * FormatAddressTest
 */
namespace Dvsa\OlcsTest\Api\Service\Helper;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\Olcs\Api\Service\Helper\FormatAddress;
use Dvsa\Olcs\Api\Entity\ContactDetails\Address as AddressEntity;

/**
 * FormatAddressTest
 */
class FormatAddressTest extends MockeryTestCase
{
    public function testFormat()
    {
        $sut = new FormatAddress();

        $line1 = 'line 1';
        $line2 = 'line 2';
        $line3 = null;
        $line4 = 'line 4';
        $town = 'town';
        $postcode = null;
        $sep = ', ';

        $address = new AddressEntity();
        $address->setAddressLine1($line1);
        $address->setAddressLine2($line2);
        $address->setAddressLine4($line4);
        $address->setTown($town);

        $expected = $line1 . $sep . $line2 . $sep . $line4 . $sep . $town;

        $this->assertEquals($expected, $sut->format($address, $sep));
    }
}
