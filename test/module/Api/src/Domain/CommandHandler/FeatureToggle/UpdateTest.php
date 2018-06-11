<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\FeatureToggle;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\FeatureToggle\Update as UpdateHandler;
use Dvsa\Olcs\Api\Domain\Repository\FeatureToggle as FeatureToggleRepo;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\FeatureToggle\Update as UpdateCmd;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle as FeatureToggleEntity;

/**
 * Update FeatureToggle Test
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class UpdateTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new UpdateHandler();
        $this->mockRepo('FeatureToggle', FeatureToggleRepo::class);

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $cmdData = [
            'id' => 999,
            'friendlyName' => 'friendly name',
            'configName' => 'config name',
            'status' => FeatureToggleEntity::ACTIVE_STATUS
        ];

        $command = UpdateCmd::create($cmdData);

        $this->repoMap['FeatureToggle']
            ->shouldReceive('save')
            ->once()
            ->with(m::type(FeatureToggleEntity::class))
            ->getMock();

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => ['FeatureToggle' => 999],
            'messages' => ["Feature toggle '999' updated"]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
