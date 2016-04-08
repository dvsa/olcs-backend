<?php

/**
 * GenerateTest.php
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Publication;

use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Publication as PublicationRepo;
use Dvsa\Olcs\Api\Domain\CommandHandler\Publication\Generate;
use Dvsa\Olcs\Api\Entity\Publication\Publication as PublicationEntity;
use Dvsa\Olcs\Api\Entity\Doc\Document as DocumentEntity;
use Dvsa\Olcs\Api\Entity\Doc\DocTemplate as DocTemplateEntity;
use Dvsa\Olcs\Api\Entity\System\Category as CategoryEntity;
use Dvsa\Olcs\Api\Entity\System\SubCategory as SubCategoryEntity;
use Dvsa\Olcs\Transfer\Command\Publication\Generate as GenerateCommand;
use Dvsa\Olcs\Api\Domain\Command\Document\GenerateAndStore as GenerateDocCommand;
use Dvsa\Olcs\Api\Domain\Command\Publication\CreateNextPublication as CreateNextPublicationCmd;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Domain\Command\Result;

/**
 * Class GenerateTest
 */
class GenerateTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new Generate();
        $this->mockRepo('Publication', PublicationRepo::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [PublicationEntity::PUB_GENERATED_STATUS];

        $this->references = [
            DocumentEntity::class => [
                2345 => m::mock(DocumentEntity::class)
            ]
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $id = 11;
        $docTemplateId = 685;
        $docCategory = 11;
        $docSubCategory = 113;
        $docDescription = 'doc description';
        $pubStatus = new RefData(PublicationEntity::PUB_GENERATED_STATUS);
        $pubType = 'A&D';
        $generatedDocId = 2345;

        $docGenerateCmdData = [
            'template' => $docTemplateId,
            'query' => [
                'publicationId' => $id,
                'pubType' => $pubType
            ],
            'description'   => $docDescription . ' generated',
            'category'      => $docCategory,
            'subCategory'   => $docSubCategory,
            'isExternal'    => true,
            'isReadOnly'    => 'N'
        ];

        $command = GenerateCommand::create(['id' => $id]);

        $document = new DocumentEntity($docTemplateId);

        $category = new CategoryEntity();
        $category->setId($docCategory);

        $subCategory = new SubCategoryEntity();
        $subCategory->setId($docSubCategory);

        $docTemplate = new DocTemplateEntity();
        $docTemplate->setDocument($document);
        $docTemplate->setCategory($category);
        $docTemplate->setSubCategory($subCategory);
        $docTemplate->setDescription($docDescription);

        $publicationEntity = m::mock(PublicationEntity::class)->makePartial();
        $publicationEntity->setId($id);
        $publicationEntity->setDocTemplate($docTemplate);
        $publicationEntity->setPubStatus($pubStatus);
        $publicationEntity->setPubType($pubType);
        $publicationEntity->shouldReceive('generate')
            ->once()
            ->with(
                $this->references[DocumentEntity::class][$generatedDocId],
                $this->refData[PublicationEntity::PUB_GENERATED_STATUS]
            )
            ->andReturnSelf();

        $docGenerationResult = new Result();
        $docGenerationResult->addId('document', $generatedDocId);
        $docGenerationResult->addMessage('Document created');

        $this->expectedSideEffect(GenerateDocCommand::class, $docGenerateCmdData, $docGenerationResult);
        $this->expectedSideEffect(CreateNextPublicationCmd::class, ['id' => $id], new Result());

        $this->repoMap['Publication']
            ->shouldReceive('fetchUsingId')
            ->once()
            ->with($command)
            ->andReturn($publicationEntity);

        $this->repoMap['Publication']->shouldReceive('save')
            ->once()
            ->with(m::type(PublicationEntity::class));

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'document' => $generatedDocId,
                'generated_publication' => $id
            ],
            'messages' => [
                'Document created',
                'Publication was generated'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
