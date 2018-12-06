<?php

namespace Dvsa\OlcsTest\Api\Domain\Command\Bus\Ebsr;

use Dvsa\Olcs\Api\Domain\Command\Bus\Ebsr\ProcessPackTransaction;
use PHPUnit_Framework_TestCase;

/**
 * Update Txc Inbox PDF Test
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class ProcessPackTransactionTest extends PHPUnit_Framework_TestCase
{
    public function testStructure()
    {
        $id = 1;
        $organisation = 2;

        $command = ProcessPackTransaction::create(
            [
                'id' => $id,
                'organisation' => $organisation,
            ]
        );

        $this->assertEquals($id, $command->getId());
        $this->assertEquals($organisation, $command->getOrganisation());
    }
}
