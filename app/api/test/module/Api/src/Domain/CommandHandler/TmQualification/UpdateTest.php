<?php

/**
 * TmQualification / Update
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\TmQualification;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\TmQualification\Update as TmQualificationUpdate;
use Dvsa\Olcs\Api\Domain\Repository\TmQualification as TmQualificationRepo;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\TmQualification\Update as Cmd;
use ZfcRbac\Service\AuthorizationService;
use Dvsa\Olcs\Api\Entity\User\Team;
use Dvsa\Olcs\Api\Entity\User\User;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\ContactDetails\Country as CountryEntity;

/**
 * TmQualification / Update
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class UpdateTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new TmQualificationUpdate();
        $this->mockRepo('TmQualification', TmQualificationRepo::class);

        $this->mockedSmServices = [
            AuthorizationService::class => m::mock(AuthorizationService::class)
        ];

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            'qtype'
        ];

        $this->references = [
            CountryEntity::class => [
                'GB' => m::mock(CountryEntity::class)
            ]
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $this->mockAuthService();

        $id = 1;
        $data = [
            'id' => $id,
            'version' => 2,
            'qualificationType' => 'qtype',
            'serialNo' => '123',
            'issuedDate' => '2015-01-01',
            'countryCode' => 'GB',
        ];

        $command = Cmd::create($data);

        $mockTmQualification = m::mock(TmQualificationEntity::class)
            ->shouldReceive('updateTmQualification')
            ->with(
                $this->refData['qtype'],
                '123',
                '2015-01-01',
                $this->references[CountryEntity::class]['GB'],
                null,
                null,
                m::type(User::class)
            )
            ->once()
            ->shouldReceive('getId')
            ->andReturn($id)
            ->once()
            ->getMock();

        $this->repoMap['TmQualification']
            ->shouldReceive('fetchById')
            ->with($id)
            ->andReturn($mockTmQualification)
            ->once()
            ->shouldReceive('save')
            ->with($mockTmQualification)
            ->once()
            ->getMock();

        $result = $this->sut->handleCommand($command);
        $this->assertEquals(
            $result->toArray(),
            [
                'id' => [
                    'tmQualification' => 1
                ],
                'messages' => [
                    'TmQualification updated successfully'
                ]
            ]
        );
    }

    protected function mockAuthService()
    {
        /** @var Team $mockTeam */
        $mockTeam = m::mock(Team::class)->makePartial();
        $mockTeam->setId(2);

        /** @var User $mockUser */
        $mockUser = m::mock(User::class)->makePartial();
        $mockUser->setId(1);
        $mockUser->setTeam($mockTeam);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('getIdentity->getUser')
            ->andReturn($mockUser);
    }
}
