<?php

/**
 * Create Office Copy Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Command\CommunityLic\Application;

use Dvsa\Olcs\Api\Domain\Command\CommunityLic\Application\CreateOfficeCopy;
use PHPUnit_Framework_TestCase;

/**
 * Create Office Copy Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class CreateOfficeCopyTest extends PHPUnit_Framework_TestCase
{
    public function testStructure()
    {
        $command = CreateOfficeCopy::create(
            [
                'licence' => 1,
                'identifier' => 2,
            ]
        );

        $this->assertEquals(1, $command->getLicence());
        $this->assertEquals(2, $command->getIdentifier());
    }
}
