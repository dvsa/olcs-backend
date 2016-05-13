<?php

/**
 * Grant Interim Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Api\Domain\Command\Document\GenerateAndStore;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Application\RefuseInterim;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\System\Category;
use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\Application\RefuseInterim as Cmd;

/**
 * Grant Interim Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class RefuseInterimTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new RefuseInterim();
        $this->mockRepo('Application', \Dvsa\Olcs\Api\Domain\Repository\Application::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            ApplicationEntity::INTERIM_STATUS_REFUSED
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $command = Cmd::create(['id' => 111]);

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();
        $licence->setId(222);

        /** @var ApplicationEntity $application */
        $application = m::mock(ApplicationEntity::class)->makePartial();
        $application->setId(111);
        $application->setIsVariation(false);
        $application->setLicence($licence);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($application)
            ->shouldReceive('save')
            ->with($application);

        $expectedQuery = [
            'licence' => 222,
            'application' => 111
        ];

        $expectedData = [
            'template' => 'NEW_APP_INT_REFUSED',
            'query' => $expectedQuery,
            'description' => 'GV Refused Interim Licence',
            'application' => 111,
            'licence' => 222,
            'category' => Category::CATEGORY_LICENSING,
            'subCategory' => Category::DOC_SUB_CATEGORY_OTHER_DOCUMENTS,
            'isExternal' => false,
            'isScan' => false,
            'busReg' => null,
            'case' => null,
            'irfoOrganisation' => null,
            'submission' => null,
            'trafficArea' => null,
            'transportManager' => null,
            'operatingCentre' => null,
            'opposition' => null,
            'issuedDate' => null,
            'dispatch' => true
        ];

        $result1 = new Result();
        $result1->addMessage('GenerateAndStore');
        $this->expectedSideEffect(GenerateAndStore::class, $expectedData, $result1);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'Interim updated',
                'GenerateAndStore'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
