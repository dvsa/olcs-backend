<?php

/**
 * Create Office Copy Test / Application
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\CommunityLic\Application;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\CommunityLic\Application\CreateOfficeCopy;
use Dvsa\Olcs\Api\Domain\Repository\CommunityLic as CommunityLicRepo;
use Dvsa\Olcs\Api\Domain\Repository\Licence as LicenceRepo;
use Dvsa\Olcs\Api\Domain\Repository\Application as ApplicationRepo;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Command\CommunityLic\Application\CreateOfficeCopy as Cmd;
use Dvsa\Olcs\Api\Domain\Command\CommunityLic\GenerateBatch as GenerateBatchCmd;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Licence\Application as ApplicationEntity;
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
        $this->sut = new CreateOfficeCopy();
        $this->mockRepo('CommunityLic', CommunityLicRepo::class);
        $this->mockRepo('Licence', LicenceRepo::class);
        $this->mockRepo('Application', ApplicationRepo::class);

        parent::setUp();
    }

    /**
     * @dataProvider licenceDataProvider
     */

    public function testHandleCommand($interimStatus)
    {
        $licenceId = 1;
        $identifier = 2;
        $data = [
            'licence' => $licenceId,
            'identifier' => $identifier
        ];

        $command = Cmd::create($data);

        $this->repoMap['Application']
            ->shouldReceive('getInterimStatus')
            ->with($identifier)
            ->andReturn($interimStatus);
        
        
        
        
        
        
        
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
