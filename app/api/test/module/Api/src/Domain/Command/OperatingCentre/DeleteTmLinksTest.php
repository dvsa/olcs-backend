<?php

/**
 * Delete Tm Links Command Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\OlcsTest\Api\Domain\Command\Tm;

use Dvsa\Olcs\Api\Domain\Command\OperatingCentre\DeleteTmLinks;
use Dvsa\Olcs\Api\Entity\OperatingCentre\OperatingCentre as OperatingCentreEntity;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Delete Tm Links Command Test
 *
  * @author Dan Eggleston <dan@stolenegg.com>
 */
class DeleteTmLinksTest extends MockeryTestCase
{
    public function testStructure()
    {
        $oc = m::mock(OperatingCentreEntity::class);

        $command = DeleteTmLinks::create(
            [
                'operatingCentre' => $oc,
            ]
        );

        $this->assertSame($oc, $command->getOperatingCentre());
    }
}
