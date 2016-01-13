<?php

/**
 * Print discs
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Discs;

use Dvsa\Olcs\Api\Domain\Command\Document\GenerateAndStore;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Command\PrintScheduler\Enqueue as EnqueueFileCommand;
use Dvsa\Olcs\Api\Domain\Command\Document\CreateDocumentSpecific as CreateDocumentSpecificCmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\Discs\PrintDiscs;
use Dvsa\Olcs\Api\Domain\Command\Discs\PrintDiscs as Cmd;
use Dvsa\Olcs\Transfer\Query\Document\DocumentList as Qry;
use Dvsa\Olcs\Api\Entity\System\Category as CategoryEntity;
use Dvsa\Olcs\Api\Entity\System\SubCategory as SubCategoryEntity;

/**
 * Print discs
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class PrintDiscsTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new PrintDiscs();

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $mockDisc = m::mock()
            ->shouldReceive('getId')
            ->andReturn(1)
            ->once()
            ->getMock();

        $template = 'GVDiscTemplate';
        $data = [
            'type' => 'Goods',
            'discs' => [$mockDisc],
            'startNumber' => 1
        ];
        $command = Cmd::create($data);

        $result1 = new Result();
        $result1->addId('identifier', 'id1');
        $saveDocData = [
            'template' => $template,
            'query' => [0 => 1],
            'knownValues' => ['Disc_List' => [['discNo' => '1']]],
            'description' => 'Vehicle discs',
            'category' => CategoryEntity::CATEGORY_LICENSING,
            'subCategory' => SubCategoryEntity::DOC_SUB_CATEGORY_DISCS,
            'isExternal' => false,
            'isScan' => false
        ];
        $this->expectedSideEffect(GenerateAndStore::class, $saveDocData, $result1);

        $printQueueData = [
            'fileIdentifier' => 'id1',
            'jobName' => 'Goods Disc List'
        ];
        $this->expectedSideEffect(EnqueueFileCommand::class, $printQueueData, new Result());

        $expected = [
            'id' => [
                'identifier' => 'id1'
            ],
            'messages' => ['Discs printed']
        ];

        $result = $this->sut->handleCommand($command);
        $this->assertEquals($expected, $result->toArray());
    }
}
