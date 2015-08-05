<?php

/**
 * DisassociateS4
 *
 * @author Joshua Curtis <josh.curtis@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\LicenceOperatingCentre;

use Mockery as m;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Command\LicenceOperatingCentre\DisassociateS4 as Cmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\LicenceOperatingCentre\DisassociateS4 as CommandHandler;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Entity\Licence\LicenceOperatingCentre;
use Dvsa\Olcs\Api\Domain\Repository;

/**
 * Class DisassociateS4
 *
 * @package Dvsa\OlcsTest\Api\Domain\CommandHandler\LicenceOperatingCentre
 * @author Joshua Curtis <josh.curtis@valtech.co.uk>
 */
class DisassociateS4Test extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CommandHandler();
        $this->mockRepo('LicenceOperatingCentre', Repository\LicenceOperatingCentre::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->references = [
            LicenceOperatingCentre::class => [
                1 => m::mock(LicenceOperatingCentre::class)->makePartial()
            ]
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $data = [
            'licenceOperatingCentres' => [
                $this->references[LicenceOperatingCentre::class][1]
            ]
        ];

        $command = Cmd::create($data);

        $this->repoMap['LicenceOperatingCentre']
            ->shouldReceive('save')
            ->once()
            ->with(m::type(LicenceOperatingCentre::class));

        $this->sut->handleCommand($command);
    }
}
