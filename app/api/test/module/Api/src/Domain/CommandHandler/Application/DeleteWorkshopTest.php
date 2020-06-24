<?php

/**
 * Delete Workshop Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Api\Domain\Command\Application\UpdateApplicationCompletion;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\Application\DeleteWorkshop;
use Dvsa\Olcs\Api\Domain\Repository\Application;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\Application\DeleteWorkshop as Cmd;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Transfer\Command\Workshop\DeleteWorkshop as WorkshopDeleteWorkshop;

/**
 * Delete Workshop Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class DeleteWorkshopTest extends CommandHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new DeleteWorkshop();
        $this->mockRepo('Application', Application::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [];

        $this->references = [];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $data = [
            'ids' => [111, 222],
            'application' => 123
        ];

        $command = Cmd::create($data);

        $expectedData = [
            'ids' => [111, 222],
            'application' => 123
        ];
        $result1 = new Result();
        $result1->addMessage('Deleted workshop');

        $this->expectedSideEffect(WorkshopDeleteWorkshop::class, $expectedData, $result1);

        $expectedData = [
            'id' => 123,
            'section' => 'safety'
        ];
        $result2 = new Result();
        $result2->addMessage('Section updated');

        $this->expectedSideEffect(UpdateApplicationCompletion::class, $expectedData, $result2);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'Deleted workshop',
                'Section updated'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
