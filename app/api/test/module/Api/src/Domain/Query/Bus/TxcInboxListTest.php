<?php

namespace Dvsa\OlcsTest\Api\Domain\Query\Bus;

use PHPUnit_Framework_TestCase;
use Dvsa\Olcs\Api\Domain\Query\Bus\TxcInboxList;

/**
 * TxcInboxList test
 */
class TxcInboxListTest extends PHPUnit_Framework_TestCase
{
    public function testStructure()
    {
        $localAuthority = 1;
        $subtype = 'subtype';
        $status = 'status';

        $query = TxcInboxList::create(
            [
                'localAuthority' => $localAuthority,
                'subType' => $subtype,
                'status' => $status
            ]
        );

        $this->assertSame($localAuthority, $query->getLocalAuthority());
        $this->assertSame($subtype, $query->getSubType());
        $this->assertSame($status, $query->getStatus());
    }
}
