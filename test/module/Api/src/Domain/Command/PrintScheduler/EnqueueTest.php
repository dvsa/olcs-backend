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
                'documentId' => 12,
                'jobName' => 'JOB_NAME',
                'user' => 1
            ]
        );

        $this->assertEquals(12, $command->getDocumentId());
        $this->assertEquals('JOB_NAME', $command->getJobName());
        $this->assertEquals(1, $command->getUser());
    }
}
