<?php

/**
 * Create Psv Discs Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Licence;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Repository\PsvDisc;
use Dvsa\Olcs\Api\Domain\CommandHandler\Licence\CreatePsvDiscs;
use Dvsa\Olcs\Transfer\Command\Licence\CreatePsvDiscs as Cmd;
use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use ZfcRbac\Service\AuthorizationService;

/**
 * Create Psv Discs Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CreatePsvDiscsTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CreatePsvDiscs();
        $this->mockRepo('PsvDisc', PsvDisc::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [

        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $this->assertTrue(true);
    }
}
