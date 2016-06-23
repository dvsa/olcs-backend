<?php

namespace Dvsa\OlcsTest\Cli\Domain\Query\CommunityLic;

use Dvsa\Olcs\Cli\Domain\Query\CommunityLic\CommunityLicencesForActivationList;

/**
 * Community licences for activation list test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class CommunityLicencesForActivationListTest extends \PHPUnit_Framework_TestCase
{
    public function testStructure()
    {
        $params = [
            'date' => 'foo'
        ];
        $command = CommunityLicencesForActivationList::create($params);
        $this->assertEquals($command->getDate(), 'foo');
    }
}
