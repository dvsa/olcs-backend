<?php

/**
 * Generate Batch Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\CommunityLic;

use Dvsa\Olcs\Api\Domain\Command\Document\CreateDocumentSpecific;
use Dvsa\Olcs\Api\Domain\Command\Document\GenerateAndStore;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Api\Entity\System\SubCategory;
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

        $docResult = new Result();
        $docResult->addId('document', 12);
        $docResult->addMessage('Create Document');
        $data = [
            'template' => $template,
            'query' => [
                'licence' => $licenceId,
                'communityLic' => $communityLicenceIds[0],
                'application' => $identifier
            ],
            'description' => 'Community licence',
            'category' => Category::CATEGORY_LICENSING,
            'subCategory' => SubCategory::DOC_SUB_CATEGORY_COMMUNITY_LICENCE,
            'isExternal' => false,
            'isScan' => false
        ];
        $this->expectedSideEffect(GenerateAndStore::class, $data, $docResult);

        $printResult = new Result();
        $printResult->addMessage('File printed');
        $printResult->addId('file', 1);
        $this->expectedSideEffect(
            EnqueueFileCmd::class,
            [
                'documentId' => 12,
                'jobName' => 'Community Licence'
            ],
            $printResult
        );

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'file' => 1,
                'document' => 12
            ],
            'messages' => [
                'Create Document',
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

        $docResult = new Result();
        $docResult->addId('document', 13);
        $docResult->addMessage('Create Document');
        $data = [
            'template' => $template,
            'query' => [
                'licence' => $licenceId,
                'communityLic' => $communityLicenceIds[0],
                'application' => $identifier
            ],
            'description' => 'Community licence',
            'category' => Category::CATEGORY_LICENSING,
            'subCategory' => SubCategory::DOC_SUB_CATEGORY_COMMUNITY_LICENCE,
            'isExternal' => false,
            'isScan' => false
        ];
        $this->expectedSideEffect(GenerateAndStore::class, $data, $docResult);

        $printResult = new Result();
        $printResult->addMessage('File printed');
        $printResult->addId('file', 1);
        $this->expectedSideEffect(
            EnqueueFileCmd::class,
            [
                'documentId' => 13,
                'jobName' => 'Community Licence'
            ],
            $printResult
        );

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'file' => 1,
                'document' => 13,
            ],
            'messages' => [
                'Create Document',
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
