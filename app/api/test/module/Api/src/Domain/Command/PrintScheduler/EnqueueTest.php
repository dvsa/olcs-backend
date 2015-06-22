<?php

namespace Dvsa\OlcsTest\Api\Domain\Command\PrintScheduler;

use Dvsa\Olcs\Api\Domain\Command\PrintScheduler\Enqueue;
use PHPUnit_Framework_TestCase;

/**
 * Enqueue
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class EnqueueTest extends PHPUnit_Framework_TestCase
{
    public function testStructure()
    {
        $command = Enqueue::create(
            [
                'fileIdentifier' => 'FILE_ID',
                'jobName' => 'JOB_NAME',
            ]
        );

        $this->assertEquals('FILE_ID', $command->getFileIdentifier());
        $this->assertEquals('JOB_NAME', $command->getJobName());
    }
}
