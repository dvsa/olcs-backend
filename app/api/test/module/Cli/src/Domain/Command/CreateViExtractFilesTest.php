<?php

/**
 * Create VI extract files test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Cli\Domain\Command;

use Dvsa\Olcs\Cli\Domain\Command\CreateViExtractFiles;

/**
 * Create VI extract files test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class CreateViExtractFilesTest extends \PHPUnit_Framework_TestCase
{
    public function testStructure()
    {
        $params = [
            'op' => true,
            'oc' => true,
            'tnm' => true,
            'vhl' => true,
            'all' => true
        ];
        $command = CreateViExtractFiles::create($params);

        foreach ($params as $key => $value) {
            $this->assertTrue($command->{'get' . ucfirst($key)}());
        }
    }
}
