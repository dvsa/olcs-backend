<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Scan;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\CommandHandler\Scan\CreateSeparatorSheet as CommandHandler;
use Dvsa\Olcs\Transfer\Command\Scan\CreateSeparatorSheet as Cmd;

use Dvsa\Olcs\Api\Domain\Repository\Fee;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\Fee\FeeType;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Task\Task;
use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Entity\Fee\Fee as FeeEntity;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea;

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
        $this->refData = [
        ];

        $this->categoryReferences = [
            9 => m::mock(\Dvsa\Olcs\Api\Entity\System\Category::class),
        ];

        $this->subCategoryReferences = [
            234 => m::mock(\Dvsa\Olcs\Api\Entity\System\SubCategory::class),
        ];

        $this->references = [
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

        $result = $this->sut->handleCommand($command);
    }

    public function testHandleCommand()
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

        $result = $this->sut->handleCommand($command);

        $this->assertSame(['scan' => 74], $result->getIds());
        $this->assertSame(['Scan ID 74 created'], $result->getMessages());
    }
}
