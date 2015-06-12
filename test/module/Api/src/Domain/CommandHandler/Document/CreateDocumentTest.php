<?php

/**
 * Create Document Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\OtherLicence;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\Document\CreateDocument;
use Dvsa\Olcs\Api\Domain\Repository\Document as DocumentRepo;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Command\Document\CreateDocument as Cmd;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\System\Category as CategoryEntity;
use Dvsa\Olcs\Api\Entity\System\SubCategory as SubCategoryEntity;
use Dvsa\Olcs\Api\Entity\Doc\Document as DocumentEntity;

/**
 * Create Document Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class CreateDocumentTest extends CommandHandlerTestCase
{
    protected $needReferences = true;

    public function setUp()
    {
        $this->sut = new CreateDocument();
        $this->mockRepo('Document', DocumentRepo::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->references = [
            LicenceEntity::class => [
                1 => m::mock(LicenceEntity::class)
            ],
            CategoryEntity::class => [
                2 => m::mock(CategoryEntity::class)
            ],
            SubCategoryEntity::class => [
                3 => m::mock(SubCategoryEntity::class)
            ],
        ];

        parent::initReferences();
    }

    /**
     * @dataProvider referencesProvider
     */
    public function testHandleCommand($licence, $category, $subCategory, $testNull)
    {
        $data = [
            'licence' => $licence,
            'category' => $category,
            'subCategory' => $subCategory,
            'issuedDate' => '2015-01-01',
            'identifier' => 4,
            'description' => 'description',
            'filename' => 'filename',
            'isExternal' => 1,
            'isReadOnly' => 1,
            'size' => 100
        ];

        /** @var OtherLicenceEntity $savedOtherLicence */
        $savedDocument = null;

        $command = Cmd::create($data);

        $this->repoMap['Document']->shouldReceive('save')
            ->once()
            ->with(DocumentEntity::class)
            ->andReturnUsing(
                function (DocumentEntity $document) use (&$savedDocument) {
                    $document->setId(1);
                    $savedDocument = $document;
                }
            );

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => ['document' => 1],
            'messages' => ['Document created successfully']
        ];

        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals($expected, $result->toArray());

        if (!$testNull) {
            $this->assertSame($this->references[LicenceEntity::class][1], $savedDocument->getLicence());
            $this->assertSame($this->references[CategoryEntity::class][2], $savedDocument->getCategory());
            $this->assertSame($this->references[SubCategoryEntity::class][3], $savedDocument->getSubCategory());
        } else {
            $this->assertNull($savedDocument->getLicence());
            $this->assertNull($savedDocument->getCategory());
            $this->assertNull($savedDocument->getSubCategory());
        }
        $this->assertEquals(
            (new \DateTime('2015-01-01'))->format('Y-m-d'),
            $savedDocument->getIssuedDate()->format('Y-m-d')
        );
        $this->assertEquals(4, $savedDocument->getIdentifier());
        $this->assertEquals('description', $savedDocument->getDescription());
        $this->assertEquals('filename', $savedDocument->getFilename());
        $this->assertEquals(1, $savedDocument->getIsExternal());
        $this->assertEquals(1, $savedDocument->getIsReadOnly());
        $this->assertEquals(100, $savedDocument->getSize());

    }

    public function referencesProvider()
    {
        return [
            [1, 2, 3, false],
            [null, null, null, true],
        ];
    }
}
