<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Publication;

use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Publication as PublicationRepo;
use Dvsa\Olcs\Api\Domain\CommandHandler\Publication\CreatePoliceDocument;
use Dvsa\Olcs\Api\Entity\Publication\Publication as PublicationEntity;
use Dvsa\Olcs\Api\Service\File\ContentStoreFileUploader;
use Dvsa\Olcs\Transfer\Command\Document\Upload as UploadCmd;
use Dvsa\Olcs\Api\Domain\Command\Publication\CreatePoliceDocument as CreatePoliceDocumentCmd;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\DocumentShare\Data\Object\File;

/**
 * Class CreatePoliceDocumentTest
 * @package Dvsa\OlcsTest\Api\Domain\CommandHandler\Publication
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class CreatePoliceDocumentTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CreatePoliceDocument();
        $this->mockRepo('Publication', PublicationRepo::class);

        $this->mockedSmServices = [
            'FileUploader' => m::mock(ContentStoreFileUploader::class)
        ];

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $id = 11;
        $docCategory = 11;
        $docSubCategory = 113;
        $docDescription = 'doc description';
        $docDescriptionPolice = $docDescription . ' police version';
        $generatedDocId = 2345;
        $filename = 'filename.rtf';
        $docFilename = '/path/to/' . $filename;
        $previousDocContent = 'previous doc content';
        $docIdentifier = 'identifier';

        $docUploadCmdData = [
            'content'       => base64_encode($previousDocContent),
            'description'   => $docDescriptionPolice,
            'category'      => $docCategory,
            'subCategory'   => $docSubCategory,
            'isExternal'    => true,
            'isReadOnly'    => 'Y',
            'filename'      => $filename
        ];

        $command = CreatePoliceDocumentCmd::create(['id' => $id]);

        $publicationEntity = m::mock(PublicationEntity::class);
        $publicationEntity->shouldReceive('getDocTemplate->getDescription')->once()->andReturn($docDescription);
        $publicationEntity->shouldReceive('getDocTemplate->getCategory->getId')->once()->andReturn($docCategory);
        $publicationEntity->shouldReceive('getDocTemplate->getSubCategory->getId')->once()->andReturn($docSubCategory);
        $publicationEntity->shouldReceive('getDocument->getFilename')->once()->andReturn($docFilename);
        $publicationEntity->shouldReceive('getDocument->getIdentifier')->once()->andReturn($docIdentifier);

        $previousDoc = m::mock(File::class);
        $previousDoc->shouldReceive('getContent')->once()->andReturn($previousDocContent);

        $this->mockedSmServices['FileUploader']
            ->shouldReceive('download')
            ->with($docIdentifier)
            ->once()
            ->andReturn($previousDoc);

        $uploadResult = new Result();
        $uploadResult->addId('document', $generatedDocId);

        $this->expectedSideEffect(UploadCmd::class, $docUploadCmdData, $uploadResult);

        $this->repoMap['Publication']
            ->shouldReceive('fetchUsingId')
            ->once()
            ->with($command)
            ->andReturn($publicationEntity);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'document' => $generatedDocId,
        ];

        $this->assertEquals($expected, $result->getIds());
    }
}
