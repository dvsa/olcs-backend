<?php

/**
 * Print Interim Document Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Api\Domain\Command\Document\DispatchDocument;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Application\PrintInterimDocument;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Api\Service\Document\DocumentGenerator;
use Dvsa\Olcs\Transfer\Command\Application\PrintInterimDocument as Cmd;
use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;

/**
 * Print Interim Document Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class PrintInterimDocumentTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new PrintInterimDocument();
        $this->mockRepo('Application', \Dvsa\Olcs\Api\Domain\Repository\Application::class);

        $this->mockedSmServices['DocumentGenerator'] = m::mock(DocumentGenerator::class);

        parent::setUp();
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
        $application->setLicence($licence);
        $application->setIsVariation(false);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($application);

        $file = m::mock();
        $file->shouldReceive('getIdentifier')
            ->andReturn('12345678')
            ->shouldReceive('getSize')
            ->andReturn(100);

        $this->mockedSmServices['DocumentGenerator']->shouldReceive('generateAndStore')
            ->with('GV_INT_LICENCE_V1', ['application' => 111, 'licence' => 222])
            ->andReturn($file);

        $expectedData = [
            'identifier' => '12345678',
            'size' => 100,
            'description' => 'GV Interim Licence',
            'filename' => 'GV_Interim_Licence.rtf',
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
            'isReadOnly' => null,
            'issuedDate' => null
        ];

        $result1 = new Result();
        $result1->addMessage('DispatchDocument');
        $this->expectedSideEffect(DispatchDocument::class, $expectedData, $result1);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'Document generated',
                'DispatchDocument'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandVariation()
    {
        $command = Cmd::create(['id' => 111]);

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();
        $licence->setId(222);

        /** @var ApplicationEntity $application */
        $application = m::mock(ApplicationEntity::class)->makePartial();
        $application->setId(111);
        $application->setLicence($licence);
        $application->setIsVariation(true);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($application);

        $file = m::mock();
        $file->shouldReceive('getIdentifier')
            ->andReturn('12345678')
            ->shouldReceive('getSize')
            ->andReturn(100);

        $this->mockedSmServices['DocumentGenerator']->shouldReceive('generateAndStore')
            ->with('GV_INT_DIRECTION_V1', ['application' => 111, 'licence' => 222])
            ->andReturn($file);

        $expectedData = [
            'identifier' => '12345678',
            'size' => 100,
            'description' => 'GV Interim Direction',
            'filename' => 'GV_Interim_Direction.rtf',
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
            'isReadOnly' => null,
            'issuedDate' => null
        ];

        $result1 = new Result();
        $result1->addMessage('DispatchDocument');
        $this->expectedSideEffect(DispatchDocument::class, $expectedData, $result1);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'Document generated',
                'DispatchDocument'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
