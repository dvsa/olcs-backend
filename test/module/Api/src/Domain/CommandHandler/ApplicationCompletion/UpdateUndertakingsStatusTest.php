<?php

/**
 * Update Undertakings Status Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\ApplicationCompletion;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\ApplicationCompletion\UpdateUndertakingsStatus as Cmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\ApplicationCompletion\UpdateUndertakingsStatus;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Mockery as m;
use Dvsa\Olcs\Api\Entity\Application\ApplicationCompletion as ApplicationCompletionEntity;

/**
 * Update Undertakings Status Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class UpdateUndertakingsStatusTest extends AbstractUpdateStatusTestCase
{
    protected $section = 'Undertakings';

    public function setUp()
    {
        $this->sut = new UpdateUndertakingsStatus();
        $this->command = Cmd::create(['id' => 111]);

        parent::setUp();
    }

    public function testHandleCommandWithChange()
    {
        $this->applicationCompletion->setUndertakingsStatus(ApplicationCompletionEntity::STATUS_NOT_STARTED);

        $this->application->setDeclarationConfirmation('N');

        $this->expectStatusChange(ApplicationCompletionEntity::STATUS_INCOMPLETE);
    }

    public function testHandleCommandWithoutChange()
    {
        $this->applicationCompletion->setUndertakingsStatus(ApplicationCompletionEntity::STATUS_INCOMPLETE);

        $this->application->setDeclarationConfirmation('N');
        
        $this->expectStatusUnchanged(ApplicationCompletionEntity::STATUS_INCOMPLETE);
    }

    public function testHandleCommand()
    {
        $this->applicationCompletion->setUndertakingsStatus(ApplicationCompletionEntity::STATUS_NOT_STARTED);

        $this->application->setDeclarationConfirmation('Y');

        $this->expectStatusChange(ApplicationCompletionEntity::STATUS_COMPLETE);
    }
}
