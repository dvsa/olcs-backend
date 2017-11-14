<?php
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Variation;

use Dvsa\Olcs\Api\Domain\Command\Application\Grant\GrantPeople;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Variation\GrantDirectorChange;
use Dvsa\Olcs\Api\Domain\Exception\BadVariationTypeException;
use Dvsa\Olcs\Api\Domain\Repository\Application as ApplicationRepository;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Transfer\Command\Application\CreateSnapshot;
use Dvsa\Olcs\Transfer\Command\Variation\GrantDirectorChange as Command;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

class GrantDirectorChangeTest extends CommandHandlerTestCase
{
    const TEST_VARIATION_ID = 'TEST_VARIATION_ID';

    public function setUp()
    {
        $this->sut = new GrantDirectorChange();

        $this->mockRepo('Application', ApplicationRepository::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            ApplicationEntity::APPLICATION_STATUS_VALID,
        ];

        parent::initReferences();
    }

    public function testCommandHandler()
    {
        $createSnapshotResult = new Result();

        $createSnapshotResult->addMessage('CREATE_SNAPSHOT_CALLED');

        $this->expectedSideEffectAsSystemUser(
            CreateSnapshot::class,
            [
                'id' => self::TEST_VARIATION_ID,
                'event' => CreateSnapshot::ON_GRANT
            ],
            $createSnapshotResult
        );

        $grantPeopleResult = new Result();

        $grantPeopleResult->addMessage('GRANT_PEOPLE_CALLED');

        $this->expectedSideEffect(
            GrantPeople::class,
            [
                'id' => self::TEST_VARIATION_ID
            ],
            $grantPeopleResult
        );

        $command = Command::create(
            [
                'id' => self::TEST_VARIATION_ID
            ]
        );

        $application = m::mock(ApplicationEntity::class)->makePartial();

        $application->shouldReceive('getVariationType')->andReturn(ApplicationEntity::VARIATION_TYPE_DIRECTOR_CHANGE);

        $application->shouldReceive('setStatus')->with($this->refData[ApplicationEntity::APPLICATION_STATUS_VALID]);
        $application->shouldReceive('setGrantedDate')->with(m::type(DateTime::class));

        $this->repoMap['Application']
            ->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($application);

        $this->repoMap['Application']
            ->shouldReceive('save')
            ->with($application);

        $result = $this->sut->handleCommand($command);

        assertThat($result->getMessages(), hasItemInArray('GRANT_PEOPLE_CALLED'));
        assertThat($result->getMessages(), hasItemInArray('CREATE_SNAPSHOT_CALLED'));
    }

    public function testThatNonDirectorChangeVariationsAreRejected()
    {
        $command = Command::create(
            [
                'id' => self::TEST_VARIATION_ID
            ]
        );

        $application = m::mock(ApplicationEntity::class)->makePartial();

        $application->shouldReceive('getVariationType')->andReturn(null);

        $this->repoMap['Application']
            ->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($application);

        $this->expectException(BadVariationTypeException::class);

        $this->sut->handleCommand($command);
    }
}
