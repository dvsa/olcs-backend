<?php

namespace Dvsa\OlcsTest\Cli\Domain\Command\Licence;

use Dvsa\Olcs\Api\Domain\Command\Licence\EndIrhpApplicationsAndPermits;
use Dvsa\Olcs\Api\Entity\WithdrawableInterface;
use PHPUnit\Framework\TestCase;

/**
 * EndIrhpApplicationsAndPermits test
 */
class EndIrhpApplicationsAndPermitsTest extends TestCase
{
    public function testStructure()
    {
        $id = 100;
        $reason = WithdrawableInterface::WITHDRAWN_REASON_PERMITS_REVOKED;

        $sut = EndIrhpApplicationsAndPermits::create(
            [
                'id' => $id,
                'reason' => $reason,
            ]
        );

        static::assertEquals($id, $sut->getId());
        static::assertEquals($reason, $sut->getReason());
        static::assertEquals(
            [
                'id' => $id,
                'reason' => $reason,
            ],
            $sut->getArrayCopy()
        );
    }
}
