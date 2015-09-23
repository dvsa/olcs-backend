<?php

/**
 * Change Business Type Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace module\Api\src\Domain\CommandHandler\Organisation;

use Dvsa\Olcs\Api\Domain\CommandHandler\Organisation\ChangeBusinessType;
use Dvsa\Olcs\Api\Domain\Repository;
use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Command\Organisation\ChangeBusinessType as Cmd;

/**
 * Change Business Type Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ChangeBusinessTypeTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->markTestSkipped();
        $this->sut = new ChangeBusinessType();
        $this->mockRepo('Organisation', Repository\Organisation::class);
        $this->mockRepo('CompanySubsidiary', Repository\CompanySubsidiary::class);
        $this->mockRepo('OrganisationPerson', Repository\OrganisationPerson::class);

        parent::setUp();
    }

    public function initReferences()
    {
        $this->refData = [];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $data = [

        ];

        $command = Cmd::create($data);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => []
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
