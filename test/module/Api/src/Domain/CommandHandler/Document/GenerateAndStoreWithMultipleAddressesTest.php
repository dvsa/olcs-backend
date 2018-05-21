<?php


namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Document;

use Dvsa\Olcs\Api\Domain\Command\Document\GenerateAndStoreWithMultipleAddresses;
use Dvsa\Olcs\Api\Domain\Repository\Document;
use Dvsa\Olcs\Api\Domain\Repository\Licence;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Command\Document\GenerateAndStoreWithMultipleAddresses as Cmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\Document\GenerateAndStoreWithMultipleAddresses as CommandHandler;
use Mockery as m;

class GenerateAndStoreWithMultipleAddressesTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CommandHandler();
        $this->mockRepo('Document', Document::class);
        $this->mockRepo('Licence', Licence::class);

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $mockCommand = m::mock(Cmd::class);



    }

}