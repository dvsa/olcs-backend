<?php

/**
 * Create Task Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Command\Task;

use Dvsa\Olcs\Api\Domain\Command\Task\CreateTask;
use PHPUnit_Framework_TestCase;

/**
 * Create Task Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CreateTaskTest extends PHPUnit_Framework_TestCase
{
    public function testStructure()
    {
        $data = [
            'foo' => 'bar',
            'category' => 111,
            'subCategory' => 222,
            'description' => 'Some Task',
            'actionDate' => '2015-01-01',
            'assignedToUser' => 333,
            'assignedToTeam' => 444,
            'isClosed' => true,
            'urgent' => true,
            'application' => 555,
            'licence' => 666
        ];

        $command = CreateTask::create($data);

        $this->assertEquals(111, $command->getCategory());
        $this->assertEquals(222, $command->getSubCategory());
        $this->assertEquals('Some Task', $command->getDescription());
        $this->assertEquals('2015-01-01', $command->getActionDate());
        $this->assertEquals(333, $command->getAssignedToUser());
        $this->assertEquals(444, $command->getAssignedToTeam());
        $this->assertEquals(true, $command->getIsClosed());
        $this->assertEquals(true, $command->getUrgent());
        $this->assertEquals(555, $command->getApplication());
        $this->assertEquals(666, $command->getLicence());

        $this->assertEquals(
            [
                'category' => 111,
                'subCategory' => 222,
                'description' => 'Some Task',
                'actionDate' => '2015-01-01',
                'assignedToUser' => 333,
                'assignedToTeam' => 444,
                'isClosed' => true,
                'urgent' => true,
                'application' => 555,
                'licence' => 666
            ],
            $command->getArrayCopy()
        );
    }
}
