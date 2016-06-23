<?php

namespace Dvsa\OlcsTest\Cli\Domain\Query\CommunityLic;

use Dvsa\Olcs\Cli\Domain\Query\CommunityLic\CommunityLicencesForSuspensionList;

/**
 * Community licences for suspension list test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class CommunityLicencesForSuspensionListTest extends \PHPUnit_Framework_TestCase
{
    public function testStructure()
    {
        $params = [
            'date' => 'foo'
        ];
        $command = CommunityLicencesForSuspensionList::create($params);
        $this->assertEquals($command->getDate(), 'foo');
    }
}
