<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Api\Domain\CommandHandler\Application\UploadEvidence;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\OperatingCentre\OperatingCentre as OperatingCentreEntity;
use Dvsa\Olcs\Api\Entity\Application\ApplicationOperatingCentre as ApplicationOperatingCentreEntity;
use Dvsa\Olcs\Api\Entity\System\Category as CategoryEntity;
use Dvsa\Olcs\Api\Entity\System\SubCategory as SubCategoryEntity;
use Dvsa\Olcs\Api\Entity\Doc\Document as DocumentEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Domain\Repository\Application as ApplicationRepo;
use Dvsa\Olcs\Api\Domain\Repository\ApplicationOperatingCentre as ApplicationOperatingCentreRepo;
use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\Application\UploadEvidence as Cmd;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;

/**
 * UploadEvidenceTest
 */
class UploadEvidenceTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new UploadEvidence();
        $this->mockRepo('Application', ApplicationRepo::class);
        $this->mockRepo('ApplicationOperatingCentre', ApplicationOperatingCentreRepo::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->categoryReferences = [
            CategoryEntity::CATEGORY_APPLICATION => m::mock(CategoryEntity::class),
        ];

        $this->subCategoryReferences = [
            SubCategoryEntity::DOC_SUB_CATEGORY_FINANCIAL_EVIDENCE_DIGITAL => m::mock(SubCategoryEntity::class),
            SubCategoryEntity::DOC_SUB_CATEGORY_ADVERT_DIGITAL => m::mock(SubCategoryEntity::class)
        ];

        $this->references = [
            OperatingCentreEntity::class => [
                222 => m::mock(OperatingCentreEntity::class)
            ]
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $command = Cmd::create(['id' => 111]);

        $documentCollection = new ArrayCollection(
            [
                new DocumentEntity('doc1'),
                new DocumentEntity('doc2'),
            ]
        );

        /** @var ApplicationEntity|m\Mock $application */
        $application = m::mock(ApplicationEntity::class)->makePartial();
        $application->setFinancialEvidenceUploaded(ApplicationEntity::FINANCIAL_EVIDENCE_SEND_IN_POST);
        $application->shouldReceive('getApplicationDocuments')
            ->with(
                $this->categoryReferences[CategoryEntity::CATEGORY_APPLICATION],
                $this->subCategoryReferences[SubCategoryEntity::DOC_SUB_CATEGORY_FINANCIAL_EVIDENCE_DIGITAL]
            )->once()->andReturn($documentCollection);

        $this->repoMap['Application']
            ->shouldReceive('fetchById')->with(111)->once()->andReturn($application)
            ->shouldReceive('save')->with($application)->once()->andReturn();

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'Financial evidence uploaded',
            ]
        ];
        $this->assertEquals($expected, $result->toArray());
        $this->assertSame(
            ApplicationEntity::FINANCIAL_EVIDENCE_UPLOADED,
            $application->getFinancialEvidenceUploaded()
        );
    }

    public function testHandleCommandNoFinancialEvidenceDocs()
    {
        $command = Cmd::create(['id' => 111]);

        $documentCollection = new ArrayCollection([]);

        /** @var ApplicationEntity|m\Mock $application */
        $application = m::mock(ApplicationEntity::class)->makePartial();
        $application->setFinancialEvidenceUploaded(ApplicationEntity::FINANCIAL_EVIDENCE_SEND_IN_POST);
        $application->shouldReceive('getApplicationDocuments')
            ->with(
                $this->categoryReferences[CategoryEntity::CATEGORY_APPLICATION],
                $this->subCategoryReferences[SubCategoryEntity::DOC_SUB_CATEGORY_FINANCIAL_EVIDENCE_DIGITAL]
            )->once()->andReturn($documentCollection);

        $this->repoMap['Application']
            ->shouldReceive('fetchById')->with(111)->once()->andReturn($application);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
            ]
        ];
        $this->assertEquals($expected, $result->toArray());
        $this->assertSame(
            ApplicationEntity::FINANCIAL_EVIDENCE_SEND_IN_POST,
            $application->getFinancialEvidenceUploaded()
        );
    }

    public function testCommandHandlerWithOc()
    {
        $command = Cmd::create(
            [
                'id' => 111,
                'operatingCentres' => [
                    [
                        'aocId' => 3,
                        'adPlacedIn' => 'foo',
                        'adPlacedDate' => '2017-01-02'
                    ]
                ]
            ]
        );
        $mockOperatingCentre = m::mock()
            ->shouldReceive('getOperatingCentre')
            ->andReturn(
                m::mock()
                    ->shouldReceive('getId')
                    ->andReturn(222)
                    ->once()
                    ->getMock()
            )
            ->shouldReceive('setAdPlaced')
            ->with(ApplicationOperatingCentreEntity::AD_UPLOAD_NOW)
            ->once()
            ->shouldReceive('setAdPlacedIn')
            ->with('foo')
            ->once()
            ->shouldReceive('setAdPlacedDate')
            ->with('2017-01-02')
            ->once()
            ->getMock();

        $documentCollection = new ArrayCollection([]);
        $documentCollectionNotEmpty = new ArrayCollection([new DocumentEntity('doc1')]);

        /** @var ApplicationEntity|m\Mock $application */
        $application = m::mock(ApplicationEntity::class);//->makePartial();
        $application
            ->shouldReceive('get')
            ->shouldReceive('getApplicationDocuments')
            ->with(
                $this->categoryReferences[CategoryEntity::CATEGORY_APPLICATION],
                $this->subCategoryReferences[SubCategoryEntity::DOC_SUB_CATEGORY_FINANCIAL_EVIDENCE_DIGITAL]
            )
            ->andReturn($documentCollection)
            ->once()
            ->shouldReceive('getApplicationDocuments')
            ->with(
                $this->categoryReferences[CategoryEntity::CATEGORY_APPLICATION],
                $this->subCategoryReferences[SubCategoryEntity::DOC_SUB_CATEGORY_ADVERT_DIGITAL],
                m::type(OperatingCentreEntity::class)
            )
            ->andReturn($documentCollectionNotEmpty)
            ->once()
            ->shouldReceive('getApplicationOperatingCentreById')
            ->with(3)
            ->andReturn($mockOperatingCentre)
            ->once()
            ->getMock();

        $this->repoMap['Application']
            ->shouldReceive('fetchById')->with(111)->once()->andReturn($application);

        $this->repoMap['ApplicationOperatingCentre']
            ->shouldReceive('save')
            ->with($mockOperatingCentre)
            ->once();

        $expected = [
            'id' => [],
            'messages' => [
                'Advert digital documents for OC 222 uploaded',
                'Advert details for OC 222 saved'
            ]
        ];
        $this->assertEquals($expected, $this->sut->handleCommand($command)->toArray());
    }

    public function testCommandHandlerNoOcException()
    {
        $this->setExpectedException(ValidationException::class);

        $command = Cmd::create(
            [
                'id' => 111,
                'operatingCentres' => [
                    [
                        'aocId' => 3,
                        'adPlacedIn' => 'foo',
                        'adPlacedDate' => '2017-01-02'
                    ]
                ]
            ]
        );
        $documentCollection = new ArrayCollection([]);

        /** @var ApplicationEntity|m\Mock $application */
        $application = m::mock(ApplicationEntity::class);//->makePartial();
        $application
            ->shouldReceive('get')
            ->shouldReceive('getApplicationDocuments')
            ->with(
                $this->categoryReferences[CategoryEntity::CATEGORY_APPLICATION],
                $this->subCategoryReferences[SubCategoryEntity::DOC_SUB_CATEGORY_FINANCIAL_EVIDENCE_DIGITAL]
            )
            ->andReturn($documentCollection)
            ->once()
            ->shouldReceive('getApplicationOperatingCentreById')
            ->with(3)
            ->andReturnNull()
            ->once()
            ->getMock();

        $this->repoMap['Application']
            ->shouldReceive('fetchById')->with(111)->once()->andReturn($application);

        $this->sut->handleCommand($command)->toArray();
    }
}
