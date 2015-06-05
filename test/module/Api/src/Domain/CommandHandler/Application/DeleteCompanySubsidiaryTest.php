<?php

/**
 * Delete Company Subsidiary Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Application\DeleteCompanySubsidiary;
use Dvsa\Olcs\Api\Domain\Command\Application\UpdateApplicationCompletion;
use Dvsa\Olcs\Transfer\Command\Licence\DeleteCompanySubsidiary as LicenceCmd;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Transfer\Command\Application\DeleteCompanySubsidiary as Cmd;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Repository\Application;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;

/**
 * Delete Company Subsidiary Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class DeleteCompanySubsidiaryTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new DeleteCompanySubsidiary();
        $this->mockRepo('Application', Application::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [

        ];

        $this->references = [

        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        /** @var LicenceEntity $application */
        $licence = m::mock(LicenceEntity::class)->makePartial();
        $licence->setId(222);

        /** @var ApplicationEntity $application */
        $application = m::mock(ApplicationEntity::class)->makePartial();
        $application->setLicence($licence);

        $data = [
            'application' => 111,
            'ids' => [1, 2, 3]
        ];

        $command = Cmd::create($data);

        $this->repoMap['Application']->shouldReceive('fetchById')
            ->with(111)
            ->andReturn($application);

        $licenceCmdData = [
            'licence' => 222,
            'ids' => [1, 2, 3]
        ];

        $result1 = new Result();
        $result1->addMessage('Company Subsidiary deleted');

        $this->expectedSideEffect(LicenceCmd::class, $licenceCmdData, $result1);

        $updateData = ['id' => 111, 'section' => 'businessDetails'];

        $result2 = new Result();
        $result2->addMessage('Section updated');

        $this->expectedSideEffect(UpdateApplicationCompletion::class, $updateData, $result2);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'Company Subsidiary deleted',
                'Section updated'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
