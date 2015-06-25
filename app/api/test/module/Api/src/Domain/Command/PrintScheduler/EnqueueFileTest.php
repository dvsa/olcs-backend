<?php

/**
 * Enqueue File Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Command\PrintScheduler;

use Dvsa\Olcs\Api\Domain\Command\PrintScheduler\EnqueueFile;
use PHPUnit_Framework_TestCase;

/**
 * Enqueue File Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class EnqueueFileTest extends PHPUnit_Framework_TestCase
{
    public function testStructure()
    {
        $command = EnqueueFile::create(
            [
                'fileId' => 'fileId',
                'jobName' => 'jobName',
                'options' => 'options'
            ]
        );

        $this->assertEquals('fileId', $command->getFileId());
        $this->assertEquals('jobName', $command->getJobName());
        $this->assertEquals('options', $command->getOptions());
    }
}
