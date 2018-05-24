<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Licence;

use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Transfer\Command\Licence\DeleteUpdateOptOutTmLetter as DeleteUpdateOptOutTmLetterCmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\Licence\DeleteUpdateOptOutTmLetter as DeleteUpdateOptOutTmLetterHandler;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\TransportManagerLicence as TmlRepo;
use Dvsa\Olcs\Transfer\Command\TransportManagerLicence\Delete as DeleteCmd;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Tm\TransportManagerLicence as TmlEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Mockery as m;

class DeleteUpdateOptOutTmLetterTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new DeleteUpdateOptOutTmLetterHandler();
        $this->mockRepo('TransportManagerLicence', TmlRepo::class);

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $tmlId = 555;
        $yesNoValue = 'Y';

        $command = DeleteUpdateOptOutTmLetterCmd::create([
            'id' => $tmlId,
            'yesNo' => $yesNoValue,
        ]);

        $licenceEntity = m::mock(LicenceEntity::class);
        $licenceEntity->shouldReceive('getTmLicences->count')->once()->withNoArgs()->andReturn(1);
        $licenceEntity->shouldReceive('setOptOutTmLetter')->once()->with($command->getYesNo());

        $tmlEntity = m::mock(TmlEntity::class);
        $tmlEntity->shouldReceive('getLicence')->once()->withNoArgs()->andReturn($licenceEntity);

        $this->repoMap['TransportManagerLicence']
            ->shouldReceive('fetchById')
            ->with($tmlId)
            ->once()
            ->andReturn($tmlEntity);

        $deleteParams = $this->deleteParams($tmlId);
        $this->expectedSideEffect(DeleteCmd::class, $deleteParams, new Result());

        $result = $this->sut->handleCommand($command);

        $this->assertSame(
            [
                'Success'
            ],
            $result->getMessages()
        );
    }

    public function testHandleCommandWithTwoTransportManagers()
    {
        $tmlId = 555;
        $yesNoValue = 'Y';

        $command = DeleteUpdateOptOutTmLetterCmd::create([
            'id' => $tmlId,
            'yesNo' => $yesNoValue,
        ]);

        $licenceEntity = m::mock(LicenceEntity::class);
        $licenceEntity->shouldReceive('getTmLicences->count')->once()->withNoArgs()->andReturn(2);

        $tmlEntity = m::mock(TmlEntity::class);
        $tmlEntity->shouldReceive('getLicence')->once()->withNoArgs()->andReturn($licenceEntity);

        $this->repoMap['TransportManagerLicence']
            ->shouldReceive('fetchById')
            ->with($tmlId)
            ->once()
            ->andReturn($tmlEntity);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage("Error: Not last Transport Manager");
        $this->sut->handleCommand($command);
    }

    /**
     * @param $tmlId
     *
     * @return array
     */
    private function deleteParams($tmlId)
    {
        return [
            'ids' => [$tmlId],
        ];
    }
}
