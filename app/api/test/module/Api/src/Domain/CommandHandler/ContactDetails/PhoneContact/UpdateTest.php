<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\ContactDetails\PhoneContact;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\CommandHandler\ContactDetails\PhoneContact\Update as Handler;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Entity;
use Dvsa\Olcs\Transfer\Command\ContactDetail\PhoneContact\Update as Cmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

/**
 * @covers Dvsa\Olcs\Api\Domain\CommandHandler\ContactDetails\PhoneContact\Update
 */
class UpdateTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new Handler();
        $this->mockRepo('PhoneContact', Repository\PhoneContact::class);
        $this->mockRepo('ContactDetails', Repository\ContactDetails::class);

        parent::setUp();
    }

    public function test()
    {
        $id = 8888;
        $contactDetailsId = 999;
        $phoneContactType = Entity\ContactDetails\PhoneContact::TYPE_HOME;

        $data = [
            'phoneNumber' => 'unit_PhoneNr',
            'phoneContactType' => $phoneContactType,
            'contactDetailsId' => $contactDetailsId,
        ];
        $command = Cmd::create($data);

        //  mock contact details repo
        $mockContactDetailsEntity = m::mock(Entity\ContactDetails\ContactDetails::class);

        $this->repoMap['ContactDetails']->shouldReceive('fetchById')
            ->with($contactDetailsId)
            ->andReturn($mockContactDetailsEntity);

        //  mock phone contact repo
        $mockPhoneContactTypeEntity = m::mock(Entity\System\RefData::class);

        $this->refData = [
            $phoneContactType => $mockPhoneContactTypeEntity,
        ];

        //  expect entity passed to save
        $mockEntity = m::mock(Entity\ContactDetails\PhoneContact::class)
            ->shouldReceive('setPhoneContactType')->once()->with($mockPhoneContactTypeEntity)->andReturnSelf()
            ->shouldReceive('setContactDetails')->once()->with($mockContactDetailsEntity)->andReturnSelf()
            ->shouldReceive('setPhoneNumber')->once()->with('unit_PhoneNr')->andReturnSelf()
            ->shouldReceive('getId')->andReturn($id)
            ->getMock();

        $this->repoMap['PhoneContact']
            ->shouldReceive('fetchUsingId')->with($command, Query::HYDRATE_OBJECT)->once()->andReturn($mockEntity)
            ->shouldReceive('save')->once()->with($mockEntity);

        $actual = $this->sut->handleCommand($command);

        static::assertEquals(['Phone contact \'' . $id . '\' updated'], $actual->getMessages());
    }
}
