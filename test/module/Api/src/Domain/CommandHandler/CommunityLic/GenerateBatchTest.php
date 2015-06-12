<?php

/**
 * Generate Batch Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\CommunityLic;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\CommunityLic\GenerateBatch;
use Dvsa\Olcs\Api\Domain\Repository\CommunityLicRepo;
use Dvsa\Olcs\Api\Domain\Repository\LicenceRepo;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Command\CommunityLic\GenerateBatch as Cmd;
use Dvsa\Olcs\Api\Domain\Command\Document\GenerateAndUploadDocument as GenerateAndUploadCmd;
use Dvsa\Olcs\Api\Domain\Command\PrintScheduler\EnqueueFile as EnqueueFileCmd;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Domain\CommandHandler\PrintScheduler\PrintSchedulerInterface;

/**
 * Create Other Licence Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class CreateOtherLicenceTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new GenerateBatch();
        $this->mockRepo('CommunityLic', CommunityLicRepo::class);
        $this->mockRepo('Licence', LicenceRepo::class);

        parent::setUp();
    }

    /**
     * @dataProvider licenceDataProvider
     */
    public function testHandleCommand($goodsOrPsv, $niFlag, $template)
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
            ->shouldReceive('getGoodsOrPsv')
            ->andReturn(
                m::mock()
                ->shouldReceive('getId')
                ->andReturn($goodsOrPsv)
                ->once()
                ->getMock()
            )
            ->once()
            ->shouldReceive('getNiFlag')
            ->andReturn($niFlag)
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
        $generateAndUploadResult = new Result();
        $generateAndUploadResult->addMessage('Document generated and uploaded');
        $generateAndUploadResult->addId('fileId', 1);
        $this->expectedSideEffect(
            GenerateAndUploadCmd::class,
            $generateAndUploadData,
            $generateAndUploadResult
        );

        $printResult = new Result();
        $printResult->addMessage('File printed');
        $printResult->addId('file', 1);
        $this->expectedSideEffect(
            EnqueueFileCmd::class,
            [
                'fileId' => 1,
                'options' => [PrintSchedulerInterface::OPTION_DOUBLE_SIDED],
                'jobName' => 'Community Licence'
            ],
            $printResult
        );

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'fileId' => 1,
                'file' => 1
            ],
            'messages' => [
                'Document generated and uploaded',
                'File printed',
                'Community Licence 10 processed'
            ]
        ];
        $this->assertEquals($expected, $result->toArray());
    }

    public function licenceDataProvider()
    {
        return [
            [LicenceEntity::LICENCE_CATEGORY_PSV, 'N', 'PSV_European_Community_Licence'],
            [LicenceEntity::LICENCE_CATEGORY_GOODS_VEHICLE, 'Y', 'GV_NI_European_Community_Licence'],
            [LicenceEntity::LICENCE_CATEGORY_GOODS_VEHICLE, 'N', 'GV_GB_European_Community_Licence'],
        ];
    }
}
