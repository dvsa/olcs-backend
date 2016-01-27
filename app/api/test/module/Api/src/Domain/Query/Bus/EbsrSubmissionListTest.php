<?php

namespace Dvsa\OlcsTest\Api\Domain\Query\Bus;

use PHPUnit_Framework_TestCase;
use Dvsa\Olcs\Api\Domain\Query\Bus\EbsrSubmissionList;

/**
 * EbsrSubmissionList test
 */
class EbsrSubmissionListTest extends PHPUnit_Framework_TestCase
{
    public function testStructure()
    {
        $organisation = 1;
        $subtype = 'subtype';
        $status = 'status';

        $query = EbsrSubmissionList::create(
            [
                'organisation' => $organisation,
                'subType' => $subtype,
                'status' => $status
            ]
        );

        $this->assertSame($organisation, $query->getOrganisation());
        $this->assertSame($subtype, $query->getSubType());
        $this->assertSame($status, $query->getStatus());
    }
}
