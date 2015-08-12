<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Disqualification;

use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\CommandHandler\Disqualification\Create as CommandHandler;
use Dvsa\Olcs\Transfer\Command\Disqualification\Create as Command;
use Dvsa\Olcs\Api\Entity\Organisation\Disqualification;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails;

/**
 * CreateTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class CreateTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CommandHandler();
        $this->mockRepo('Disqualification', \Dvsa\Olcs\Api\Domain\Repository\Disqualification::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [];

        $this->references = [
            Organisation::class => [
                154 => m::mock(Organisation::class)
            ],
            ContactDetails::class => [
                43 => m::mock(ContactDetails::class)
            ]
        ];

        parent::initReferences();
    }

    public function testHandleCommandOrganisation()
    {
        $data = [
            'organisation' => 154,
            'notes' => 'NOTES',
            'startDate' => '2015-08-07',
            'isDisqualified' => 'Y',
            'period' => 12,
        ];
        $command = Command::create($data);

        $this->repoMap['Disqualification']->shouldReceive('save')->once()->andReturnUsing(
            function (Disqualification $saveDisqualification) use ($data) {
                $saveDisqualification->setId(154);
                $this->assertSame(
                    $this->references[Organisation::class][154], $saveDisqualification->getOrganisation()
                );
                $this->assertSame($data['notes'], $saveDisqualification->getNotes());
                $this->assertEquals(new \DateTime($data['startDate']), $saveDisqualification->getStartDate());
                $this->assertSame($data['isDisqualified'], $saveDisqualification->getIsDisqualified());
                $this->assertSame($data['period'], $saveDisqualification->getPeriod());
            }
        );

        $result = $this->sut->handleCommand($command);

        $this->assertEquals(['Disqualification created'], $result->getMessages());
        $this->assertEquals(['disqualification' => 154], $result->getIds());
    }

    public function testHandleCommandOfficer()
    {
        $data = [
            'officerCd' => 43,
            'notes' => 'NOTES',
            'startDate' => '2015-08-07',
            'isDisqualified' => 'Y',
            'period' => 12,
        ];
        $command = Command::create($data);

        $this->repoMap['Disqualification']->shouldReceive('save')->once()->andReturnUsing(
            function (Disqualification $saveDisqualification) use ($data) {
                $saveDisqualification->setId(154);
                $this->assertSame($this->references[ContactDetails::class][43], $saveDisqualification->getOfficerCd());
                $this->assertSame($data['notes'], $saveDisqualification->getNotes());
                $this->assertEquals(new \DateTime($data['startDate']), $saveDisqualification->getStartDate());
                $this->assertSame($data['isDisqualified'], $saveDisqualification->getIsDisqualified());
                $this->assertSame($data['period'], $saveDisqualification->getPeriod());
            }
        );

        $result = $this->sut->handleCommand($command);

        $this->assertEquals(['Disqualification created'], $result->getMessages());
        $this->assertEquals(['disqualification' => 154], $result->getIds());
    }
}
