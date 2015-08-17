<?php

/**
 * Generate Batch Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\CommunityLic;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Repository;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\CommunityLic\GenerateBatch;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Command\CommunityLic\GenerateBatch as Cmd;
use Dvsa\Olcs\Api\Domain\Command\PrintScheduler\Enqueue as EnqueueFileCmd;

/**
 * Create Other Licence Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class GenerateBatchTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new GenerateBatch();
        $this->mockRepo('CommunityLic', Repository\CommunityLic::class);
        $this->mockRepo('Licence', Repository\Licence::class);
        $this->mockRepo('Application', Repository\Application::class);
        $this->mockedSmServices = ['DocumentGenerator' => m::mock('Dvsa\Olcs\Api\Service\Document\DocumentGenerator')];

        parent::setUp();
    }

    /**
     * @dataProvider licenceDataProvider
     */
    public function testHandleCommand($isPsv, $niFlag, $template)
    {
        $licenceId = 1;
        $identifier = null;
        $communityLicenceIds = [10];
        $data = [
            'licence' => $licenceId,
            'communityLicenceIds' => $communityLicenceIds,
            'identifier' => $identifier
        ];

        $command = Cmd::create($data);

        $mockLicence = m::mock()
            ->shouldReceive('isPsv')
            ->andReturn($isPsv)
            ->once()
            ->shouldReceive('getNiFlag')
            ->andReturn($niFlag)
            ->shouldReceive('getId')
            ->andReturn(1)
            ->getMock();

        $this->repoMap['Licence']
            ->shouldReceive('fetchById')
            ->with($licenceId)
            ->andReturn($mockLicence);

        $generateAndUploadData = [
            'template' => $template,
            'data'     => [
                'licence' => $licenceId,
                'communityLic' => $communityLicenceIds[0],
                'application' => $identifier
            ],
            'folder'   => 'documents',
            'fileName' => 'Community Licence'

        ];

        $this->mockedSmServices['DocumentGenerator']
            ->shouldReceive('generateFromTemplate')
            ->with($template, $generateAndUploadData['data'])
            ->andReturn('document')
            ->once()
            ->shouldReceive('uploadGeneratedContent')
            ->with('document', 'documents')
            ->andReturn(
                m::mock()
                ->shouldReceive('getIdentifier')
                ->andReturn(1)
                ->once()
                ->getMock()
            )
            ->once()
            ->getMock();

        $printResult = new Result();
        $printResult->addMessage('File printed');
        $printResult->addId('file', 1);
        $this->expectedSideEffect(
            EnqueueFileCmd::class,
            [
                'fileIdentifier' => 1,
                'jobName' => 'Community Licence'
            ],
            $printResult
        );

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'file' => 1
            ],
            'messages' => [
                'File printed',
                'Community Licence 10 processed'
            ]
        ];
        $this->assertEquals($expected, $result->toArray());
    }

    /**
     * @dataProvider licenceDataProvider
     */
    public function testHandleCommandApplication($isPsv, $niFlag, $template)
    {
        $licenceId = 1;
        $identifier = 2;
        $communityLicenceIds = [10];
        $data = [
            'licence' => $licenceId,
            'communityLicenceIds' => $communityLicenceIds,
            'identifier' => $identifier
        ];

        $command = Cmd::create($data);

        $mockLicence = m::mock()
            ->shouldReceive('getId')
            ->andReturn(1)
            ->getMock();

        $mockApplication = m::mock()
            ->shouldReceive('isPsv')
            ->andReturn($isPsv)
            ->once()
            ->shouldReceive('getNiFlag')
            ->andReturn($niFlag)
            ->shouldReceive('getId')
            ->andReturn(2)
            ->shouldReceive('getLicence')
            ->andReturn($mockLicence)
            ->getMock();

        $this->repoMap['Application']
            ->shouldReceive('fetchById')
            ->with($identifier)
            ->andReturn($mockApplication);

        $generateAndUploadData = [
            'template' => $template,
            'data'     => [
                'licence' => $licenceId,
                'communityLic' => $communityLicenceIds[0],
                'application' => $identifier
            ],
            'folder'   => 'documents',
            'fileName' => 'Community Licence'
        ];

        $this->mockedSmServices['DocumentGenerator']
            ->shouldReceive('generateFromTemplate')
            ->with($template, $generateAndUploadData['data'])
            ->andReturn('document')
            ->once()
            ->shouldReceive('uploadGeneratedContent')
            ->with('document', 'documents')
            ->andReturn(
                m::mock()
                    ->shouldReceive('getIdentifier')
                    ->andReturn(1)
                    ->once()
                    ->getMock()
            )
            ->once()
            ->getMock();

        $printResult = new Result();
        $printResult->addMessage('File printed');
        $printResult->addId('file', 1);
        $this->expectedSideEffect(
            EnqueueFileCmd::class,
            [
                'fileIdentifier' => 1,
                'jobName' => 'Community Licence'
            ],
            $printResult
        );

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'file' => 1
            ],
            'messages' => [
                'File printed',
                'Community Licence 10 processed'
            ]
        ];
        $this->assertEquals($expected, $result->toArray());
    }

    public function licenceDataProvider()
    {
        return [
            [true, 'N', 'PSV_European_Community_Licence'],
            [false, 'Y', 'GV_NI_European_Community_Licence'],
            [false, 'N', 'GV_GB_European_Community_Licence'],
        ];
    }
}
