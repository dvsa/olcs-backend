<?php

/**
 * Generate Batch Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Command\CommunityLic;

use Dvsa\Olcs\Api\Domain\Command\CommunityLic\GenerateBatch;
use PHPUnit_Framework_TestCase;

/**
 * Generate Batch Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class GenerateBatchTest extends PHPUnit_Framework_TestCase
{
    public function testStructure()
    {
        $command = GenerateBatch::create(
            [
                'licence' => 1,
                'communityLicenceIds' => [10, 20],
                'identifier' => 2,
            ]
        );

        $this->assertEquals(1, $command->getLicence());
        $this->assertEquals([10, 20], $command->getCommunityLicenceIds());
        $this->assertEquals(2, $command->getIdentifier());
    }
}
