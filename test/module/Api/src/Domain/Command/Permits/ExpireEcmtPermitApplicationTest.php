<?php

namespace Dvsa\OlcsTest\Api\Domain\Command\Permits;

use Dvsa\Olcs\Api\Domain\Command\Permits\ExpireEcmtPermitApplication;

/**
 * Set EcmtPermitApplication status to expired test
 */
class ExpireEcmtPermitApplicationTest extends \PHPUnit\Framework\TestCase
{
    public function testStructure()
    {
        $data = [ 'id' => 1 ];
        $command = ExpireEcmtPermitApplication::create($data);
        static::assertEquals(1, $command->getId());
    }
}
