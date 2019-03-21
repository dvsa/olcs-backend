<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\IrhpPermit;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Document\GenerateAndStore;
use Dvsa\Olcs\Api\Domain\Command\IrhpPermit\GenerateCoverLetterDocument as Cmd;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\IrhpPermit\GenerateCoverLetterDocument as Sut;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermit as IrhpPermitRepo;
use Dvsa\Olcs\Api\Entity\Doc\Document as DocumentEntity;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication as EcmtPermitApplicationEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermit as IrhpPermitEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication as IrhpPermitApplicationEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType as IrhpPermitTypeEntity;
use Dvsa\Olcs\Api\Entity\System\Category as CategoryEntity;
use Dvsa\Olcs\Api\Entity\System\SubCategory as SubCategoryEntity;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

/**
 * GenerateCoverLetterDocumentTest
 */
class GenerateCoverLetterDocumentTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new Sut();
        $this->mockRepo('IrhpPermit', IrhpPermitRepo::class);

        parent::setUp();
    }

    /**
    * @dataProvider dpHandleCommand
    */
    public function testHandleCommand($irhpPermitTypeId, $expectedTemplate, $expectedDescription, $expectedMessages)
    {
        $irhpPermitId = 1;
        $licenceId = 10;

        $command = Cmd::Create(
            [
                'irhpPermit' => $irhpPermitId
            ]
        );

        $irhpPermitType = m::mock(IrhpPermitTypeEntity::class);
        $irhpPermitType->shouldReceive('getId')
            ->andReturn($irhpPermitTypeId);

        $irhpPermitApplication = m::mock(IrhpPermitApplicationEntity::class);
        $irhpPermitApplication->shouldReceive('getIrhpPermitWindow->getIrhpPermitStock->getIrhpPermitType')
            ->andReturn($irhpPermitType);
        $irhpPermitApplication->shouldReceive('getRelatedApplication->getLicence->getId')
            ->andReturn($licenceId);

        $irhpPermit = m::mock(IrhpPermitEntity::class);
        $irhpPermit->shouldReceive('getIrhpPermitApplication')->andReturn($irhpPermitApplication);
        $irhpPermit->shouldReceive('getId')->andReturn($irhpPermitId);

        $this->repoMap['IrhpPermit']->shouldReceive('fetchById')
            ->with($irhpPermitId, Query::HYDRATE_OBJECT)
            ->andReturn($irhpPermit);

        $this->expectedSideEffect(
            GenerateAndStore::class,
            [
                'template' => $expectedTemplate,
                'query' => [
                    'licence' => $licenceId,
                    'irhpPermit' => $irhpPermitId,
                ],
                'knownValues' => [],
                'description' => $expectedDescription,
                'category' => CategoryEntity::CATEGORY_PERMITS,
                'subCategory' => SubCategoryEntity::DOC_SUB_CATEGORY_PERMIT_COVERING_LETTER,
                'isExternal' => false,
                'isScan' => false
            ],
            (new Result())->addId('document', 100)->addMessage('Document generated')
        );

        $result = $this->sut->handleCommand($command);

        $this->assertInstanceOf(Result::class, $result);
        $expected = [
            'id' => [
                'coveringLetter' => 100,
            ],
            'messages' => $expectedMessages
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function dpHandleCommand()
    {
        return [
            'ECMT' => [
                'irhpPermitTypeId' => IrhpPermitTypeEntity::IRHP_PERMIT_TYPE_ID_ECMT,
                'expectedTemplate' => EcmtPermitApplicationEntity::PERMIT_COVERING_LETTER_TEMPLATE_NAME,
                'expectedDescription' => 'IRHP PERMIT ECMT COVERING LETTER 1',
                'expectedMessages' => [
                    'IRHP PERMIT ECMT COVERING LETTER 1 RTF created and stored',
                ],
            ],
            'IRHP Bilateral' => [
                'irhpPermitTypeId' => IrhpPermitTypeEntity::IRHP_PERMIT_TYPE_ID_BILATERAL,
                'expectedTemplate' => DocumentEntity::IRHP_PERMIT_ANN_BILAT_COVERING_LETTER,
                'expectedDescription' => 'IRHP PERMIT ANN BILAT COVERING LETTER 1',
                'expectedMessages' => [
                    'IRHP PERMIT ANN BILAT COVERING LETTER 1 RTF created and stored',
                ],
            ],
        ];
    }

    /**
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     * @expectedExceptionMessage Cover letter template not defined for IRHP Permit Type (id: undefined)
     */
    public function testHandleCommandForUndefinedTemplate()
    {
        $irhpPermitTypeId = 'undefined';
        $irhpPermitId = 1;

        $command = Cmd::Create(
            [
                'irhpPermit' => $irhpPermitId
            ]
        );

        $irhpPermitType = m::mock(IrhpPermitTypeEntity::class);
        $irhpPermitType->shouldReceive('getId')
            ->andReturn($irhpPermitTypeId);

        $irhpPermitApplication = m::mock(IrhpPermitApplicationEntity::class);
        $irhpPermitApplication->shouldReceive('getIrhpPermitWindow->getIrhpPermitStock->getIrhpPermitType')
            ->andReturn($irhpPermitType);

        $irhpPermit = m::mock(IrhpPermitEntity::class);
        $irhpPermit->shouldReceive('getIrhpPermitApplication')->andReturn($irhpPermitApplication);

        $this->repoMap['IrhpPermit']->shouldReceive('fetchById')
            ->with($irhpPermitId, Query::HYDRATE_OBJECT)
            ->andReturn($irhpPermit);

        $this->sut->handleCommand($command);
    }
}
