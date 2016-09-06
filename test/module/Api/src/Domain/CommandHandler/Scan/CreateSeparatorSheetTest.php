<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Scan;

use Dvsa\Olcs\Api\Domain\Command\Document\CreateDocumentSpecific;
use Dvsa\Olcs\Api\Domain\Command\Document\GenerateAndStore;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Scan\CreateSeparatorSheet as CommandHandler;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Api\Entity\System\SubCategory;
use Dvsa\Olcs\Transfer\Command\Scan\CreateSeparatorSheet as Cmd;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;

/**
 * CreateSeparatorSheetTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class CreateSeparatorSheetTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CommandHandler();
        $this->mockRepo('Scan', \Dvsa\Olcs\Api\Domain\Repository\Scan::class);
        $this->mockRepo('Licence', \Dvsa\Olcs\Api\Domain\Repository\Licence::class);
        $this->mockRepo('Bus', \Dvsa\Olcs\Api\Domain\Repository\Bus::class);
        $this->mockRepo('BusRegSearchView', \Dvsa\Olcs\Api\Domain\Repository\BusRegSearchView::class);
        $this->mockRepo('Cases', \Dvsa\Olcs\Api\Domain\Repository\Cases::class);
        $this->mockRepo('Organisation', \Dvsa\Olcs\Api\Domain\Repository\Organisation::class);
        $this->mockRepo('TransportManager', \Dvsa\Olcs\Api\Domain\Repository\TransportManager::class);
        $this->mockRepo('Category', \Dvsa\Olcs\Api\Domain\Repository\Category::class);
        $this->mockRepo('SubCategory', \Dvsa\Olcs\Api\Domain\Repository\SubCategory::class);
        $this->mockRepo('SubCategoryDescription', \Dvsa\Olcs\Api\Domain\Repository\SubCategoryDescription::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [];

        $this->categoryReferences = [
            1 => m::mock(\Dvsa\Olcs\Api\Entity\System\Category::class),
            2 => m::mock(\Dvsa\Olcs\Api\Entity\System\Category::class),
            3 => m::mock(\Dvsa\Olcs\Api\Entity\System\Category::class),
            5 => m::mock(\Dvsa\Olcs\Api\Entity\System\Category::class),
            7 => m::mock(\Dvsa\Olcs\Api\Entity\System\Category::class),
            8 => m::mock(\Dvsa\Olcs\Api\Entity\System\Category::class),
            9 => m::mock(\Dvsa\Olcs\Api\Entity\System\Category::class),
        ];

        $this->subCategoryReferences = [
            92 => m::mock(\Dvsa\Olcs\Api\Entity\System\SubCategory::class),
            234 => m::mock(\Dvsa\Olcs\Api\Entity\System\SubCategory::class),
        ];

        $this->references = [
            \Dvsa\Olcs\Api\Entity\Organisation\Organisation::class => [
                'entityIdentifier' => m::mock(\Dvsa\Olcs\Api\Entity\Organisation\Organisation::class),
            ],
            \Dvsa\Olcs\Api\Entity\Tm\TransportManager::class => [
                'entityIdentifier' => m::mock(\Dvsa\Olcs\Api\Entity\Tm\TransportManager::class),
            ]
        ];

        parent::initReferences();
    }

    public function testHandleCommandNoDesc()
    {
        $command = Cmd::create(
            [
                'categoryId' => 9,
                'subCategoryId' => 234,
                'entityIdentifier' => 'entityIdentifier',
                'descriptionId' => null,
                'description' => null,
            ]
        );

        $this->setExpectedException(\Dvsa\Olcs\Api\Domain\Exception\ValidationException::class);

        $this->sut->handleCommand($command);
    }

    public function testHandleCommandInvalidCategory()
    {
        $command = Cmd::create(
            [
                'categoryId' => 234,
                'subCategoryId' => 234,
                'entityIdentifier' => 'entityIdentifier',
                'descriptionId' => 21,
                'description' => 'NOT USED',
            ]
        );

        $this->setExpectedException(\Dvsa\Olcs\Api\Domain\Exception\ValidationException::class);

        $this->sut->handleCommand($command);
    }

    public function testHandleCommandNoEntityForCategory()
    {
        $sut = m::mock(CommandHandler::class)->makePartial();
        $this->setExpectedException(\Dvsa\Olcs\Api\Domain\Exception\ValidationException::class);
        $sut->getEntityTypeForCategory(-1);
    }

    public function testHandleCommandWithDescriptionId()
    {
        $command = Cmd::create(
            [
                'categoryId' => 9,
                'subCategoryId' => 234,
                'entityIdentifier' => 'entityIdentifier',
                'descriptionId' => 44,
                'description' => 'NEVER',
            ]
        );

        $licence = m::mock(Licence::class)->makePartial();
        $licence->setId(32);
        $licence->setLicNo('LIC001');

        $category = new \Dvsa\Olcs\Api\Entity\System\Category();
        $category->setDescription('CAT_NAME');

        $subCategory = new \Dvsa\Olcs\Api\Entity\System\SubCategory();
        $subCategory->setSubCategoryName('SUB_CAT_NAME');

        $subCategoryDescription = new \Dvsa\Olcs\Api\Entity\System\SubCategoryDescription();
        $subCategoryDescription->setDescription('DESCRIPTION');

        $this->repoMap['SubCategoryDescription']->shouldReceive('fetchById')->with(44)->once()
            ->andReturn($subCategoryDescription);

        $this->repoMap['Licence']->shouldReceive('fetchByLicNo')->with('entityIdentifier')->once()
            ->andReturn($licence);

        $this->repoMap['Scan']->shouldReceive('save')->with(m::type(\Dvsa\Olcs\Api\Entity\PrintScan\Scan::class))
            ->once()->andReturnUsing(
                function (\Dvsa\Olcs\Api\Entity\PrintScan\Scan $scan) use ($licence) {
                    $scan->setId(74);
                    $this->assertSame($this->categoryReferences[9], $scan->getCategory());
                    $this->assertSame($this->subCategoryReferences[234], $scan->getSubCategory());
                    $this->assertSame('DESCRIPTION', $scan->getDescription());
                    $this->assertSame($licence, $scan->getLicence());
                }
            );

        $this->repoMap['Category']->shouldReceive('fetchById')->with(9)->once()->andReturn($category);
        $this->repoMap['SubCategory']->shouldReceive('fetchById')->with(234)->once()->andReturn($subCategory);

        $result1 = new Result();
        $result1->addMessage('Create Document');
        $result1->addId('document', 654);
        $dtoData = [
            'template' => 'Scanning_SeparatorSheet',
            'query' => [],
            'knownValues' => [
                'DOC_CATEGORY_ID_SCAN'       => 9,
                'DOC_CATEGORY_NAME_SCAN'     => 'CAT_NAME',
                'LICENCE_NUMBER_SCAN'        => 'LIC001',
                'LICENCE_NUMBER_REPEAT_SCAN' => 'LIC001',
                'ENTITY_ID_TYPE_SCAN'        => 'Licence',
                'ENTITY_ID_SCAN'             => 32,
                'ENTITY_ID_REPEAT_SCAN'      => 32,
                'DOC_SUBCATEGORY_ID_SCAN'    => 234,
                'DOC_SUBCATEGORY_NAME_SCAN'  => 'SUB_CAT_NAME',
                'DOC_DESCRIPTION_ID_SCAN'    => 74,
                'DOC_DESCRIPTION_NAME_SCAN'  => 'DESCRIPTION',
            ],
            'description' => 'Scanning separator',
            'category' => Category::CATEGORY_LICENSING,
            'subCategory' => SubCategory::DOC_SUB_CATEGORY_SCANNING_SEPARATOR,
            'isExternal' => false,
            'isScan' => false
        ];
        $this->expectedSideEffect(GenerateAndStore::class, $dtoData, $result1);

        $this->expectedSideEffect(
            \Dvsa\Olcs\Api\Domain\Command\PrintScheduler\Enqueue::class,
            [
                'documentId' => 654,
                'jobName' => 'Scanning Separator Sheet'
            ],
            new \Dvsa\Olcs\Api\Domain\Command\Result()
        );

        $result = $this->sut->handleCommand($command);

        $this->assertSame(['document' => 654, 'scan' => 74], $result->getIds());
        $this->assertSame(['Create Document', 'Scan ID 74 created'], $result->getMessages());
    }

    public function testHandleCommandCategoryApplication()
    {
        $command = Cmd::create(
            [
                'categoryId' => 9,
                'subCategoryId' => 234,
                'entityIdentifier' => 'entityIdentifier',
                'descriptionId' => null,
                'description' => 'TEST 1',
            ]
        );

        $licence = m::mock(Licence::class)->makePartial();
        $licence->setId(32);
        $licence->setLicNo('LIC001');

        $category = new \Dvsa\Olcs\Api\Entity\System\Category();
        $category->setDescription('CAT_NAME');

        $subCategory = new \Dvsa\Olcs\Api\Entity\System\SubCategory();
        $subCategory->setSubCategoryName('SUB_CAT_NAME');

        $this->repoMap['Licence']->shouldReceive('fetchByLicNo')->with('entityIdentifier')->once()
            ->andReturn($licence);

        $this->repoMap['Scan']->shouldReceive('save')->with(m::type(\Dvsa\Olcs\Api\Entity\PrintScan\Scan::class))
            ->once()->andReturnUsing(
                function (\Dvsa\Olcs\Api\Entity\PrintScan\Scan $scan) use ($licence) {
                    $scan->setId(74);
                    $this->assertSame($this->categoryReferences[9], $scan->getCategory());
                    $this->assertSame($this->subCategoryReferences[234], $scan->getSubCategory());
                    $this->assertSame('TEST 1', $scan->getDescription());
                    $this->assertSame($licence, $scan->getLicence());
                }
            );

        $this->repoMap['Category']->shouldReceive('fetchById')->with(9)->once()->andReturn($category);
        $this->repoMap['SubCategory']->shouldReceive('fetchById')->with(234)->once()->andReturn($subCategory);

        $result1 = new Result();
        $result1->addMessage('Create Document');
        $result1->addId('document', 987);
        $dtoData = [
            'template' => 'Scanning_SeparatorSheet',
            'query' => [],
            'knownValues' => [
                'DOC_CATEGORY_ID_SCAN'       => 9,
                'DOC_CATEGORY_NAME_SCAN'     => 'CAT_NAME',
                'LICENCE_NUMBER_SCAN'        => 'LIC001',
                'LICENCE_NUMBER_REPEAT_SCAN' => 'LIC001',
                'ENTITY_ID_TYPE_SCAN'        => 'Licence',
                'ENTITY_ID_SCAN'             => 32,
                'ENTITY_ID_REPEAT_SCAN'      => 32,
                'DOC_SUBCATEGORY_ID_SCAN'    => 234,
                'DOC_SUBCATEGORY_NAME_SCAN'  => 'SUB_CAT_NAME',
                'DOC_DESCRIPTION_ID_SCAN'    => 74,
                'DOC_DESCRIPTION_NAME_SCAN'  => 'TEST 1',
            ],
            'description' => 'Scanning separator',
            'category' => Category::CATEGORY_LICENSING,
            'subCategory' => SubCategory::DOC_SUB_CATEGORY_SCANNING_SEPARATOR,
            'isExternal' => false,
            'isScan' => false
        ];
        $this->expectedSideEffect(GenerateAndStore::class, $dtoData, $result1);

        $this->expectedSideEffect(
            \Dvsa\Olcs\Api\Domain\Command\PrintScheduler\Enqueue::class,
            [
                'documentId' => 987,
                'jobName' => 'Scanning Separator Sheet'
            ],
            new \Dvsa\Olcs\Api\Domain\Command\Result()
        );

        $result = $this->sut->handleCommand($command);

        $this->assertSame(['document' => 987, 'scan' => 74], $result->getIds());
        $this->assertSame(['Create Document', 'Scan ID 74 created'], $result->getMessages());
    }

    public function testHandleCommandCategoryLicence()
    {
        $command = Cmd::create(
            [
                'categoryId' => 1,
                'subCategoryId' => 92,
                'entityIdentifier' => 'entityIdentifier',
                'descriptionId' => null,
                'description' => 'TEST 1',
            ]
        );

        $licence = m::mock(Licence::class)->makePartial();
        $licence->setId(32);
        $licence->setLicNo('LIC001');

        $category = new \Dvsa\Olcs\Api\Entity\System\Category();
        $category->setDescription('CAT_NAME');

        $subCategory = new \Dvsa\Olcs\Api\Entity\System\SubCategory();
        $subCategory->setSubCategoryName('SUB_CAT_NAME');

        $this->repoMap['Licence']->shouldReceive('fetchByLicNo')->with('entityIdentifier')->once()
            ->andReturn($licence);

        $this->repoMap['Scan']->shouldReceive('save')->with(m::type(\Dvsa\Olcs\Api\Entity\PrintScan\Scan::class))
            ->once()->andReturnUsing(
                function (\Dvsa\Olcs\Api\Entity\PrintScan\Scan $scan) use ($licence) {
                    $scan->setId(74);
                    $this->assertSame($this->categoryReferences[1], $scan->getCategory());
                    $this->assertSame($this->subCategoryReferences[92], $scan->getSubCategory());
                    $this->assertSame('TEST 1', $scan->getDescription());
                    $this->assertSame($licence, $scan->getLicence());
                }
            );

        $this->repoMap['Category']->shouldReceive('fetchById')->with(1)->once()->andReturn($category);
        $this->repoMap['SubCategory']->shouldReceive('fetchById')->with(92)->once()->andReturn($subCategory);

        $result1 = new Result();
        $result1->addMessage('Create Document');
        $result1->addId('document', 342);
        $dtoData = [
            'template' => 'Scanning_SeparatorSheet',
            'query' => [],
            'knownValues' => [
                'DOC_CATEGORY_ID_SCAN'       => 1,
                'DOC_CATEGORY_NAME_SCAN'     => 'CAT_NAME',
                'LICENCE_NUMBER_SCAN'        => 'LIC001',
                'LICENCE_NUMBER_REPEAT_SCAN' => 'LIC001',
                'ENTITY_ID_TYPE_SCAN'        => 'Licence',
                'ENTITY_ID_SCAN'             => 32,
                'ENTITY_ID_REPEAT_SCAN'      => 32,
                'DOC_SUBCATEGORY_ID_SCAN'    => 92,
                'DOC_SUBCATEGORY_NAME_SCAN'  => 'SUB_CAT_NAME',
                'DOC_DESCRIPTION_ID_SCAN'    => 74,
                'DOC_DESCRIPTION_NAME_SCAN'  => 'TEST 1',
            ],
            'description' => 'Scanning separator',
            'category' => Category::CATEGORY_LICENSING,
            'subCategory' => SubCategory::DOC_SUB_CATEGORY_SCANNING_SEPARATOR,
            'isExternal' => false,
            'isScan' => false
        ];
        $this->expectedSideEffect(GenerateAndStore::class, $dtoData, $result1);

        $this->expectedSideEffect(
            \Dvsa\Olcs\Api\Domain\Command\PrintScheduler\Enqueue::class,
            [
                'documentId' => 342,
                'jobName' => 'Scanning Separator Sheet'
            ],
            new \Dvsa\Olcs\Api\Domain\Command\Result()
        );

        $result = $this->sut->handleCommand($command);

        $this->assertSame(['document' => 342, 'scan' => 74], $result->getIds());
        $this->assertSame(['Create Document', 'Scan ID 74 created'], $result->getMessages());
    }

    public function testHandleCommandCategoryEnvironmental()
    {
        $command = Cmd::create(
            [
                'categoryId' => 7,
                'subCategoryId' => 92,
                'entityIdentifier' => 'entityIdentifier',
                'descriptionId' => null,
                'description' => 'TEST 1',
            ]
        );

        $licence = m::mock(Licence::class)->makePartial();
        $licence->setId(32);
        $licence->setLicNo('LIC001');

        $category = new \Dvsa\Olcs\Api\Entity\System\Category();
        $category->setDescription('CAT_NAME');

        $subCategory = new \Dvsa\Olcs\Api\Entity\System\SubCategory();
        $subCategory->setSubCategoryName('SUB_CAT_NAME');

        $this->repoMap['Licence']->shouldReceive('fetchByLicNo')->with('entityIdentifier')->once()
            ->andReturn($licence);

        $this->repoMap['Scan']->shouldReceive('save')->with(m::type(\Dvsa\Olcs\Api\Entity\PrintScan\Scan::class))
            ->once()->andReturnUsing(
                function (\Dvsa\Olcs\Api\Entity\PrintScan\Scan $scan) use ($licence) {
                    $scan->setId(74);
                    $this->assertSame($this->categoryReferences[7], $scan->getCategory());
                    $this->assertSame($this->subCategoryReferences[92], $scan->getSubCategory());
                    $this->assertSame('TEST 1', $scan->getDescription());
                    $this->assertSame($licence, $scan->getLicence());
                }
            );

        $this->repoMap['Category']->shouldReceive('fetchById')->with(7)->once()->andReturn($category);
        $this->repoMap['SubCategory']->shouldReceive('fetchById')->with(92)->once()->andReturn($subCategory);

        $result1 = new Result();
        $result1->addMessage('Create Document');
        $result1->addId('document', 123);
        $dtoData = [
            'template' => 'Scanning_SeparatorSheet',
            'query' => [],
            'knownValues' => [
                'DOC_CATEGORY_ID_SCAN'       => 7,
                'DOC_CATEGORY_NAME_SCAN'     => 'CAT_NAME',
                'LICENCE_NUMBER_SCAN'        => 'LIC001',
                'LICENCE_NUMBER_REPEAT_SCAN' => 'LIC001',
                'ENTITY_ID_TYPE_SCAN'        => 'Licence',
                'ENTITY_ID_SCAN'             => 32,
                'ENTITY_ID_REPEAT_SCAN'      => 32,
                'DOC_SUBCATEGORY_ID_SCAN'    => 92,
                'DOC_SUBCATEGORY_NAME_SCAN'  => 'SUB_CAT_NAME',
                'DOC_DESCRIPTION_ID_SCAN'    => 74,
                'DOC_DESCRIPTION_NAME_SCAN'  => 'TEST 1',
            ],
            'description' => 'Scanning separator',
            'category' => Category::CATEGORY_LICENSING,
            'subCategory' => SubCategory::DOC_SUB_CATEGORY_SCANNING_SEPARATOR,
            'isExternal' => false,
            'isScan' => false
        ];
        $this->expectedSideEffect(GenerateAndStore::class, $dtoData, $result1);

        $this->expectedSideEffect(
            \Dvsa\Olcs\Api\Domain\Command\PrintScheduler\Enqueue::class,
            [
                'documentId' => 123,
                'jobName' => 'Scanning Separator Sheet'
            ],
            new \Dvsa\Olcs\Api\Domain\Command\Result()
        );

        $result = $this->sut->handleCommand($command);

        $this->assertSame(['document' => 123, 'scan' => 74], $result->getIds());
        $this->assertSame(['Create Document', 'Scan ID 74 created'], $result->getMessages());
    }

    public function testHandleCommandCategoryCompliance()
    {
        $command = Cmd::create(
            [
                'categoryId' => 2,
                'subCategoryId' => 92,
                'entityIdentifier' => 'entityIdentifier',
                'descriptionId' => null,
                'description' => 'TEST 1',
            ]
        );

        $licence = m::mock(Licence::class)->makePartial();
        $licence->setId(32);
        $licence->setLicNo('LIC001');

        $cases = m::mock(\Dvsa\Olcs\Api\Entity\Cases\Cases::class)->makePartial();
        $cases->setId(35);
        $cases->setLicence($licence);
        $cases->setTransportManager('TM');

        $category = new \Dvsa\Olcs\Api\Entity\System\Category();
        $category->setDescription('CAT_NAME');

        $subCategory = new \Dvsa\Olcs\Api\Entity\System\SubCategory();
        $subCategory->setSubCategoryName('SUB_CAT_NAME');

        $this->repoMap['Cases']->shouldReceive('fetchById')->with('entityIdentifier')->once()
            ->andReturn($cases);

        $this->repoMap['Scan']->shouldReceive('save')->with(m::type(\Dvsa\Olcs\Api\Entity\PrintScan\Scan::class))
            ->once()->andReturnUsing(
                function (\Dvsa\Olcs\Api\Entity\PrintScan\Scan $scan) use ($cases) {
                    $scan->setId(74);
                    $this->assertSame($this->categoryReferences[2], $scan->getCategory());
                    $this->assertSame($this->subCategoryReferences[92], $scan->getSubCategory());
                    $this->assertSame('TEST 1', $scan->getDescription());
                    $this->assertSame($cases, $scan->getCase());
                    $this->assertSame($cases->getLicence(), $scan->getLicence());
                    $this->assertSame($cases->getTransportManager(), $scan->getTransportManager());
                }
            );

        $this->repoMap['Category']->shouldReceive('fetchById')->with(2)->once()->andReturn($category);
        $this->repoMap['SubCategory']->shouldReceive('fetchById')->with(92)->once()->andReturn($subCategory);

        $result1 = new Result();
        $result1->addMessage('Create Document');
        $result1->addId('document', 124);
        $dtoData = [
            'template' => 'Scanning_SeparatorSheet',
            'query' => [],
            'knownValues' => [
                'DOC_CATEGORY_ID_SCAN'       => 2,
                'DOC_CATEGORY_NAME_SCAN'     => 'CAT_NAME',
                'LICENCE_NUMBER_SCAN'        => 'LIC001',
                'LICENCE_NUMBER_REPEAT_SCAN' => 'LIC001',
                'ENTITY_ID_TYPE_SCAN'        => 'Case',
                'ENTITY_ID_SCAN'             => 35,
                'ENTITY_ID_REPEAT_SCAN'      => 35,
                'DOC_SUBCATEGORY_ID_SCAN'    => 92,
                'DOC_SUBCATEGORY_NAME_SCAN'  => 'SUB_CAT_NAME',
                'DOC_DESCRIPTION_ID_SCAN'    => 74,
                'DOC_DESCRIPTION_NAME_SCAN'  => 'TEST 1',
            ],
            'description' => 'Scanning separator',
            'category' => Category::CATEGORY_LICENSING,
            'subCategory' => SubCategory::DOC_SUB_CATEGORY_SCANNING_SEPARATOR,
            'isExternal' => false,
            'isScan' => false
        ];
        $this->expectedSideEffect(GenerateAndStore::class, $dtoData, $result1);

        $this->expectedSideEffect(
            \Dvsa\Olcs\Api\Domain\Command\PrintScheduler\Enqueue::class,
            [
                'documentId' => 124,
                'jobName' => 'Scanning Separator Sheet'
            ],
            new \Dvsa\Olcs\Api\Domain\Command\Result()
        );

        $result = $this->sut->handleCommand($command);

        $this->assertSame(['document' => 124, 'scan' => 74], $result->getIds());
        $this->assertSame(['Create Document', 'Scan ID 74 created'], $result->getMessages());
    }

    public function testHandleCommandCategoryIrfo()
    {
        $command = Cmd::create(
            [
                'categoryId' => 8,
                'subCategoryId' => 92,
                'entityIdentifier' => 'entityIdentifier',
                'descriptionId' => null,
                'description' => 'TEST 1',
            ]
        );

        $licence = m::mock(Licence::class)->makePartial();
        $licence->setId(32);
        $licence->setLicNo('LIC001');

        $category = new \Dvsa\Olcs\Api\Entity\System\Category();
        $category->setDescription('CAT_NAME');

        $subCategory = new \Dvsa\Olcs\Api\Entity\System\SubCategory();
        $subCategory->setSubCategoryName('SUB_CAT_NAME');

        $this->repoMap['Scan']->shouldReceive('save')->with(m::type(\Dvsa\Olcs\Api\Entity\PrintScan\Scan::class))
            ->once()->andReturnUsing(
                function (\Dvsa\Olcs\Api\Entity\PrintScan\Scan $scan) {
                    $scan->setId(74);
                    $this->assertSame($this->categoryReferences[8], $scan->getCategory());
                    $this->assertSame($this->subCategoryReferences[92], $scan->getSubCategory());
                    $this->assertSame('TEST 1', $scan->getDescription());
                    $this->assertSame(
                        $this->references[\Dvsa\Olcs\Api\Entity\Organisation\Organisation::class]['entityIdentifier'],
                        $scan->getIrfoOrganisation()
                    );
                }
            );

        $this->repoMap['Category']->shouldReceive('fetchById')->with(8)->once()->andReturn($category);
        $this->repoMap['SubCategory']->shouldReceive('fetchById')->with(92)->once()->andReturn($subCategory);

        $result1 = new Result();
        $result1->addMessage('Create Document');
        $result1->addId('document', 125);
        $dtoData = [
            'template' => 'Scanning_SeparatorSheet',
            'query' => [],
            'knownValues' => [
                'DOC_CATEGORY_ID_SCAN'       => 8,
                'DOC_CATEGORY_NAME_SCAN'     => 'CAT_NAME',
                'LICENCE_NUMBER_SCAN'        => 'Unknown',
                'LICENCE_NUMBER_REPEAT_SCAN' => 'Unknown',
                'ENTITY_ID_TYPE_SCAN'        => 'IRFO',
                'ENTITY_ID_SCAN'             => 'entityIdentifier',
                'ENTITY_ID_REPEAT_SCAN'      => 'entityIdentifier',
                'DOC_SUBCATEGORY_ID_SCAN'    => 92,
                'DOC_SUBCATEGORY_NAME_SCAN'  => 'SUB_CAT_NAME',
                'DOC_DESCRIPTION_ID_SCAN'    => 74,
                'DOC_DESCRIPTION_NAME_SCAN'  => 'TEST 1',
            ],
            'description' => 'Scanning separator',
            'category' => Category::CATEGORY_LICENSING,
            'subCategory' => SubCategory::DOC_SUB_CATEGORY_SCANNING_SEPARATOR,
            'isExternal' => false,
            'isScan' => false
        ];
        $this->expectedSideEffect(GenerateAndStore::class, $dtoData, $result1);

        $this->expectedSideEffect(
            \Dvsa\Olcs\Api\Domain\Command\PrintScheduler\Enqueue::class,
            [
                'documentId' => 125,
                'jobName' => 'Scanning Separator Sheet'
            ],
            new \Dvsa\Olcs\Api\Domain\Command\Result()
        );

        $result = $this->sut->handleCommand($command);

        $this->assertSame(['document' => 125, 'scan' => 74], $result->getIds());
        $this->assertSame(['Create Document', 'Scan ID 74 created'], $result->getMessages());
    }

    public function testHandleCommandCategoryTransportManager()
    {
        $command = Cmd::create(
            [
                'categoryId' => 5,
                'subCategoryId' => 92,
                'entityIdentifier' => 'entityIdentifier',
                'descriptionId' => null,
                'description' => 'TEST 1',
            ]
        );

        $category = new \Dvsa\Olcs\Api\Entity\System\Category();
        $category->setDescription('CAT_NAME');

        $subCategory = new \Dvsa\Olcs\Api\Entity\System\SubCategory();
        $subCategory->setSubCategoryName('SUB_CAT_NAME');

        $this->repoMap['Scan']->shouldReceive('save')->with(m::type(\Dvsa\Olcs\Api\Entity\PrintScan\Scan::class))
            ->once()->andReturnUsing(
                function (\Dvsa\Olcs\Api\Entity\PrintScan\Scan $scan) {
                    $scan->setId(74);
                    $this->assertSame($this->categoryReferences[5], $scan->getCategory());
                    $this->assertSame($this->subCategoryReferences[92], $scan->getSubCategory());
                    $this->assertSame('TEST 1', $scan->getDescription());
                    $this->assertSame(
                        $this->references[\Dvsa\Olcs\Api\Entity\Tm\TransportManager::class]['entityIdentifier'],
                        $scan->getTransportManager()
                    );
                }
            );

        $this->repoMap['Category']->shouldReceive('fetchById')->with(5)->once()->andReturn($category);
        $this->repoMap['SubCategory']->shouldReceive('fetchById')->with(92)->once()->andReturn($subCategory);

        $result1 = new Result();
        $result1->addMessage('Create Document');
        $result1->addId('document', 134);
        $dtoData = [
            'template' => 'Scanning_SeparatorSheet',
            'query' => [],
            'knownValues' => [
                'DOC_CATEGORY_ID_SCAN'       => 5,
                'DOC_CATEGORY_NAME_SCAN'     => 'CAT_NAME',
                'LICENCE_NUMBER_SCAN'        => 'Unknown',
                'LICENCE_NUMBER_REPEAT_SCAN' => 'Unknown',
                'ENTITY_ID_TYPE_SCAN'        => 'Transport Manager',
                'ENTITY_ID_SCAN'             => 'entityIdentifier',
                'ENTITY_ID_REPEAT_SCAN'      => 'entityIdentifier',
                'DOC_SUBCATEGORY_ID_SCAN'    => 92,
                'DOC_SUBCATEGORY_NAME_SCAN'  => 'SUB_CAT_NAME',
                'DOC_DESCRIPTION_ID_SCAN'    => 74,
                'DOC_DESCRIPTION_NAME_SCAN'  => 'TEST 1',
            ],
            'description' => 'Scanning separator',
            'category' => Category::CATEGORY_LICENSING,
            'subCategory' => SubCategory::DOC_SUB_CATEGORY_SCANNING_SEPARATOR,
            'isExternal' => false,
            'isScan' => false
        ];
        $this->expectedSideEffect(GenerateAndStore::class, $dtoData, $result1);

        $this->expectedSideEffect(
            \Dvsa\Olcs\Api\Domain\Command\PrintScheduler\Enqueue::class,
            [
                'documentId' => 134,
                'jobName' => 'Scanning Separator Sheet'
            ],
            new \Dvsa\Olcs\Api\Domain\Command\Result()
        );

        $result = $this->sut->handleCommand($command);

        $this->assertSame(['document' => 134, 'scan' => 74], $result->getIds());
        $this->assertSame(['Create Document', 'Scan ID 74 created'], $result->getMessages());
    }

    public function testHandleCommandCategoryBusReg()
    {
        $command = Cmd::create(
            [
                'categoryId' => 3,
                'subCategoryId' => 92,
                'entityIdentifier' => 'entityIdentifier',
                'descriptionId' => null,
                'description' => 'TEST 1',
            ]
        );

        $licence = m::mock(Licence::class)->makePartial();
        $licence->setId(32);
        $licence->setLicNo('LIC001');

        $category = new \Dvsa\Olcs\Api\Entity\System\Category();
        $category->setDescription('CAT_NAME');

        $subCategory = new \Dvsa\Olcs\Api\Entity\System\SubCategory();
        $subCategory->setSubCategoryName('SUB_CAT_NAME');

        $busRegSearchView = new \Dvsa\Olcs\Api\Entity\View\BusRegSearchView();
        $busRegSearchView->setId(783);

        $busReg = new \Dvsa\Olcs\Api\Entity\Bus\BusReg();
        $busReg->setLicence($licence);
        $busReg->setId(88);

        $this->repoMap['BusRegSearchView']->shouldReceive('fetchByRegNo')->with('entityIdentifier')->once()
            ->andReturn($busRegSearchView);
        $this->repoMap['Bus']->shouldReceive('fetchById')->with(783)->once()->andReturn($busReg);

        $this->repoMap['Scan']->shouldReceive('save')->with(m::type(\Dvsa\Olcs\Api\Entity\PrintScan\Scan::class))
            ->once()->andReturnUsing(
                function (\Dvsa\Olcs\Api\Entity\PrintScan\Scan $scan) use ($busReg) {
                    $scan->setId(74);
                    $this->assertSame($this->categoryReferences[3], $scan->getCategory());
                    $this->assertSame($this->subCategoryReferences[92], $scan->getSubCategory());
                    $this->assertSame('TEST 1', $scan->getDescription());
                    $this->assertSame($busReg, $scan->getBusReg());
                    $this->assertSame($busReg->getLicence(), $scan->getLicence());
                }
            );

        $this->repoMap['Category']->shouldReceive('fetchById')->with(3)->once()->andReturn($category);
        $this->repoMap['SubCategory']->shouldReceive('fetchById')->with(92)->once()->andReturn($subCategory);

        $result1 = new Result();
        $result1->addMessage('Create Document');
        $result1->addId('document', 123);
        $dtoData = [
            'template' => 'Scanning_SeparatorSheet',
            'query' => [],
            'knownValues' => [
                'DOC_CATEGORY_ID_SCAN'       => 3,
                'DOC_CATEGORY_NAME_SCAN'     => 'CAT_NAME',
                'LICENCE_NUMBER_SCAN'        => 'LIC001',
                'LICENCE_NUMBER_REPEAT_SCAN' => 'LIC001',
                'ENTITY_ID_TYPE_SCAN'        => 'Bus Route',
                'ENTITY_ID_SCAN'             => 88,
                'ENTITY_ID_REPEAT_SCAN'      => 88,
                'DOC_SUBCATEGORY_ID_SCAN'    => 92,
                'DOC_SUBCATEGORY_NAME_SCAN'  => 'SUB_CAT_NAME',
                'DOC_DESCRIPTION_ID_SCAN'    => 74,
                'DOC_DESCRIPTION_NAME_SCAN'  => 'TEST 1',
            ],
            'description' => 'Scanning separator',
            'category' => Category::CATEGORY_LICENSING,
            'subCategory' => SubCategory::DOC_SUB_CATEGORY_SCANNING_SEPARATOR,
            'isExternal' => false,
            'isScan' => false
        ];
        $this->expectedSideEffect(GenerateAndStore::class, $dtoData, $result1);

        $this->expectedSideEffect(
            \Dvsa\Olcs\Api\Domain\Command\PrintScheduler\Enqueue::class,
            [
                'documentId' => 123,
                'jobName' => 'Scanning Separator Sheet'
            ],
            new \Dvsa\Olcs\Api\Domain\Command\Result()
        );

        $result = $this->sut->handleCommand($command);

        $this->assertSame(['document' => 123, 'scan' => 74], $result->getIds());
        $this->assertSame(['Create Document', 'Scan ID 74 created'], $result->getMessages());
    }
}
