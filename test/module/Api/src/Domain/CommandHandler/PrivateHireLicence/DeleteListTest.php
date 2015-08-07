<?php

/**
 * DeleteListTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\PrivateHireLicence;

use Dvsa\Olcs\Api\Domain\CommandHandler\PrivateHireLicence\DeleteList as CommandHandler;
use Dvsa\Olcs\Transfer\Command\PrivateHireLicence\DeleteList as Command;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;

/**
 * DeleteListTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class DeleteListTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CommandHandler();
        $this->mockRepo('PrivateHireLicence', \Dvsa\Olcs\Api\Domain\Repository\PrivateHireLicence::class);
        $this->mockRepo('Licence', \Dvsa\Olcs\Api\Domain\Repository\Licence::class);

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $command = Command::create(['ids' => [4323, 12373]]);

        $licence = new \Dvsa\Olcs\Api\Entity\Licence\Licence(
            new \Dvsa\Olcs\Api\Entity\Organisation\Organisation(new \Dvsa\Olcs\Api\Entity\System\RefData()),
            new \Dvsa\Olcs\Api\Entity\System\RefData()
        );
        $licence->setTrafficArea('FOO');
        $phl1 = new \Dvsa\Olcs\Api\Entity\Licence\PrivateHireLicence();
        $phl1->setLicence($licence);
        $phl2 = new \Dvsa\Olcs\Api\Entity\Licence\PrivateHireLicence();
        $phl2->setLicence($licence);

        $this->repoMap['PrivateHireLicence']->shouldReceive('fetchById')->with(4323)->once()->andReturn($phl1);
        $this->repoMap['PrivateHireLicence']->shouldReceive('delete')->with($phl1)->once()->andReturn();
        $this->repoMap['PrivateHireLicence']->shouldReceive('fetchById')->with(12373)->once()->andReturn($phl2);
        $this->repoMap['PrivateHireLicence']->shouldReceive('delete')->with($phl2)->once()->andReturn();

        $response = $this->sut->handleCommand($command);

        $this->assertSame(
            [
                'PrivateHireLicence ID 4323 deleted',
                'PrivateHireLicence ID 12373 deleted',
            ],
            $response->getMessages()
        );
    }

    public function testHandleCommandNotNullTrafficArea()
    {
        $command = Command::create(['ids' => [4323, 12373]]);

        $licence = new \Dvsa\Olcs\Api\Entity\Licence\Licence(
            new \Dvsa\Olcs\Api\Entity\Organisation\Organisation(new \Dvsa\Olcs\Api\Entity\System\RefData()),
            new \Dvsa\Olcs\Api\Entity\System\RefData()
        );
        $licence->setTrafficArea('FOO');
        $licence->addPrivateHireLicences('SOME');
        $phl1 = new \Dvsa\Olcs\Api\Entity\Licence\PrivateHireLicence();
        $phl1->setLicence($licence);
        $phl2 = new \Dvsa\Olcs\Api\Entity\Licence\PrivateHireLicence();
        $phl2->setLicence($licence);

        $this->repoMap['PrivateHireLicence']->shouldReceive('fetchById')->with(4323)->once()->andReturn($phl1);
        $this->repoMap['PrivateHireLicence']->shouldReceive('delete')->with($phl1)->once()->andReturn();
        $this->repoMap['PrivateHireLicence']->shouldReceive('fetchById')->with(12373)->once()->andReturn($phl2);
        $this->repoMap['PrivateHireLicence']->shouldReceive('delete')->with($phl2)->once()->andReturn();

        $response = $this->sut->handleCommand($command);

        $this->assertSame(
            [
                'PrivateHireLicence ID 4323 deleted',
                'PrivateHireLicence ID 12373 deleted',
            ],
            $response->getMessages()
        );
    }
}
