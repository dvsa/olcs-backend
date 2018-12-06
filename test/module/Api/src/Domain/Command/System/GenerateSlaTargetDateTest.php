<?php

namespace Dvsa\OlcsTest\Api\Domain\Command\System;

use Dvsa\Olcs\Api\Domain\Command\System\GenerateSlaTargetDate;
use PHPUnit_Framework_TestCase;

class GenerateSlaTargetDateTest extends PHPUnit_Framework_TestCase
{
    public function testStructure()
    {
        $data = [
            'pi' => 10,
            'submission' => 11,
            'proposeToRevoke' => 12,
            'statement' => 13,
        ];
        $command = GenerateSlaTargetDate::create($data);

        $this->assertEquals($data['pi'], $command->getPi());
        $this->assertEquals($data['submission'], $command->getSubmission());
        $this->assertEquals($data['proposeToRevoke'], $command->getProposeToRevoke());
        $this->assertEquals($data['statement'], $command->getStatement());
    }
}
