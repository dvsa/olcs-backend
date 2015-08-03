<?php

/**
 * Create Operating Centre Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Licence;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Entity\Licence\LicenceOperatingCentre;
use Dvsa\Olcs\Api\Entity\OperatingCentre\OperatingCentre;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\Licence\CreateOperatingCentre as CommandHandler;
use Dvsa\Olcs\Api\Domain\Service\OperatingCentreHelper;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Transfer\Command\Licence\CreateOperatingCentre as Cmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use ZfcRbac\Service\AuthorizationService;
use Dvsa\Olcs\Api\Domain\Repository;

/**
 * Create Operating Centre Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CreateOperatingCentreTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CommandHandler();
        $this->mockRepo('Licence', Repository\Licence::class);
        $this->mockRepo('Document', Repository\Document::class);
        $this->mockRepo('OperatingCentre', Repository\OperatingCentre::class);
        $this->mockRepo('LicenceOperatingCentre', Repository\LicenceOperatingCentre::class);

        $this->mockedSmServices['OperatingCentreHelper'] = m::mock(OperatingCentreHelper::class);
        $this->mockedSmServices[AuthorizationService::class] = m::mock(AuthorizationService::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [];

        parent::initReferences();
    }

    public function testHandleCommandWithoutPermission()
    {
        $data = [];
        $command = Cmd::create($data);

        $this->setExpectedException(ForbiddenException::class);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('isGranted')
            ->with(Permission::INTERNAL_USER, null)
            ->andReturn(false);

        $this->sut->handleCommand($command);
    }

    public function testHandleCommand()
    {
        $data = [
            'licence' => 111
        ];
        $command = Cmd::create($data);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('isGranted')
            ->with(Permission::INTERNAL_USER, null)
            ->andReturn(true);

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();
        $licence->initCollections();

        $this->repoMap['Licence']->shouldReceive('fetchById')
            ->with(111)
            ->andReturn($licence);

        $oc = m::mock(OperatingCentre::class)->makePartial();

        $this->mockedSmServices['OperatingCentreHelper']->shouldReceive('validate')
            ->once()
            ->with($licence, $command)
            ->shouldReceive('createOperatingCentre')
            ->once()
            ->with($command, $this->commandHandler, m::type(Result::class), $this->repoMap['OperatingCentre'])
            ->andReturn($oc)
            ->shouldReceive('saveDocuments')
            ->with($licence, $oc, $this->repoMap['Document'])
            ->shouldReceive('updateOperatingCentreLink')
            ->once()
            ->with(
                m::type(LicenceOperatingCentre::class),
                $licence,
                $command,
                $this->repoMap['LicenceOperatingCentre']
            );

        $this->sut->handleCommand($command);

        $this->assertEquals(1, $licence->getOperatingCentres()->count());

        /** @var LicenceOperatingCentre $savedLoc */
        $savedLoc = $licence->getOperatingCentres()->first();
        $savedOc = $savedLoc->getOperatingCentre();

        $this->assertSame($oc, $savedOc);
    }
}
