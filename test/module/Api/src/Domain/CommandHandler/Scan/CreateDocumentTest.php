<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Scan;

use Dvsa\Olcs\Transfer\Command\Scan\CreateDocument as Cmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\Scan\CreateDocument as CommandHandler;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

/**
 * CreateDocumentTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class CreateDocumentTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CommandHandler();
        $this->mockRepo('Scan', \Dvsa\Olcs\Api\Domain\Repository\Scan::class);

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $data = [
            'scanId' => 124,
            'fileIdentifier' => 'abcdefg1234',
            'fileName' => 'document.pdf',
            'fileSize' => 23454,
        ];
        $command = Cmd::create($data);

        $scan = new \Dvsa\Olcs\Api\Entity\PrintScan\Scan();
        $scan->setId(124);
        $scan->setLicence(m::mock()->shouldReceive('getId')->andReturn(61)->getMock());
        $scan->setBusReg(m::mock()->shouldReceive('getId')->andReturn(62)->getMock());
        $scan->setCase(m::mock()->shouldReceive('getId')->andReturn(63)->getMock());
        $scan->setTransportManager(m::mock()->shouldReceive('getId')->andReturn(64)->getMock());
        $scan->setCategory(m::mock()->shouldReceive('getId')->andReturn(65)->getMock());
        $scan->setSubCategory(m::mock()->shouldReceive('getId')->andReturn(66)->getMock());
        $scan->setIrfoOrganisation(m::mock()->shouldReceive('getId')->andReturn(67)->getMock());
        $scan->setDescription('DESCRIPTION');

        $this->repoMap['Scan']->shouldReceive('fetchById')->with(124)->once()->andReturn($scan);

        $this->expectedSideEffect(
            \Dvsa\Olcs\Api\Domain\Command\Document\CreateDocumentSpecific::class,
            [
                'identifier'        => 'abcdefg1234',
                'description'       => 'DESCRIPTION',
                'filename'          => 'document.pdf',
                'isExternal'        => false,
                'isReadOnly'        => true,
                'isScan'            => true,
                // @todo fix this once we have a solution to test the current date/time
                'issuedDate'        => (new \DateTime())->format(\DateTime::W3C),
                'size'              => 23454,
                'licence'           => 61,
                'busReg'            => 62,
                'case'              => 63,
                'transportManager'  => 64,
                'category'          => 65,
                'subCategory'       => 66,
                'irfoOrganisation'  => 67,
                'application' => null,
                'submission' => null,
                'trafficArea' => null,
                'operatingCentre' => null,
                'opposition' => null,
            ],
            new \Dvsa\Olcs\Api\Domain\Command\Result()
        );

        $this->expectedSideEffect(
            \Dvsa\Olcs\Api\Domain\Command\Task\CreateTask::class,
            [
                'category'          => 65,
                'subCategory'       => 66,
                'description'       => 'DESCRIPTION',
                'licence'           => 61,
                'busReg'            => 62,
                'case'              => 63,
                'transportManager'  => 64,
                'irfoOrganisation'  => 67,
                'actionDate'        => null,
                'assignedToUser'    => null,
                'assignedToTeam'    => null,
                'isClosed'          => false,
                'urgent'            => false,
                'application'       => null,
            ],
            new \Dvsa\Olcs\Api\Domain\Command\Result()
        );

        $this->repoMap['Scan']->shouldReceive('delete')->with($scan)->once();

        $response = $this->sut->handleCommand($command);

        $this->assertSame(['scan' => 124], $response->getIds());
    }
}
