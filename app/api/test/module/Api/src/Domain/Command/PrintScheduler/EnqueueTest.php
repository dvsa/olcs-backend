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
                'documents' => [101, 102],
                'type' => 'TYPE',
                'jobName' => 'JOB_NAME',
                'user' => 1,
                'copies' => 10,
            ]
        );

        $this->assertEquals(12, $command->getDocumentId());
        $this->assertEquals([101, 102], $command->getDocuments());
        $this->assertEquals('TYPE', $command->getType());
        $this->assertEquals('JOB_NAME', $command->getJobName());
        $this->assertEquals(1, $command->getUser());
        $this->assertEquals(10, $command->getCopies());
        $this->assertEquals(false, $command->getIsDiscPrinting());
        $this->assertEquals(
            [
                'documentId' => 12,
                'documents' => [101, 102],
                'type' => 'TYPE',
                'jobName' => 'JOB_NAME',
                'user' => 1,
                'copies' => 10,
                'isDiscPrinting' => false,
            ],
            $command->getArrayCopy()
        );
    }
}
