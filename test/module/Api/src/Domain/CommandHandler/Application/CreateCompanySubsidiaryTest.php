<?php

/**
 * Create Company Subsidiary Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Application\CreateCompanySubsidiary;
use Dvsa\Olcs\Api\Domain\Command\Application\UpdateApplicationCompletion;
use Dvsa\Olcs\Transfer\Command\Licence\CreateCompanySubsidiary as LicenceCmd;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Transfer\Command\Application\CreateCompanySubsidiary as Cmd;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Repository\Application;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;

/**
 * Create Company Subsidiary Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CreateCompanySubsidiaryTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CreateCompanySubsidiary();
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
            'name' => 'foo',
            'companyNo' => '12345678'
        ];

        $command = Cmd::create($data);

        $this->repoMap['Application']->shouldReceive('fetchById')
            ->with(111)
            ->andReturn($application);

        $licenceCmdData = [
            'licence' => 222,
            'name' => 'foo',
            'companyNo' => '12345678'
        ];

        $result1 = new Result();
        $result1->addId('companySubsidiary', 1);
        $result1->addMessage('Company Subsidiary created');

        $this->expectedSideEffect(LicenceCmd::class, $licenceCmdData, $result1);

        $updateData = ['id' => 111, 'section' => 'businessDetails'];

        $result2 = new Result();
        $result2->addMessage('Section updated');

        $this->expectedSideEffect(UpdateApplicationCompletion::class, $updateData, $result2);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'companySubsidiary' => 1
            ],
            'messages' => [
                'Company Subsidiary created',
                'Section updated'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
