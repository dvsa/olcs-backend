<?php

namespace Dvsa\OlcsTest\Cli\Domain\Command\CommunityLic;

use Dvsa\Olcs\Cli\Domain\Command\CommunityLic\Suspend;

/**
 * Suspend command test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class SuspendTest extends \PHPUnit_Framework_TestCase
{
    public function testStructure()
    {
        $params = [
            'communityLicenceIds' => [1, 2]
        ];
        $command = Suspend::create($params);
        $this->assertEquals($command->getCommunityLicenceIds(), [1, 2]);
    }
}
