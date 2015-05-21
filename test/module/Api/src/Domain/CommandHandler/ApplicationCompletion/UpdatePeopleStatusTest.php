<?php

/**
 * Update People Status Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\ApplicationCompletion;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\ApplicationCompletion\UpdatePeopleStatus as Cmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\ApplicationCompletion\UpdatePeopleStatus;
use Mockery as m;
use Dvsa\Olcs\Api\Entity\Application\ApplicationCompletion as ApplicationCompletionEntity;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;

/**
 * Update People Status Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class UpdatePeopleStatusTest extends AbstractUpdateStatusTestCase
{
    /**
     * @var Organisation
     */
    protected $organisation;

    protected $section = 'People';

    public function setUp()
    {
        $this->sut = new UpdatePeopleStatus();
        $this->command = Cmd::create(['id' => 111]);

        parent::setUp();

        $this->organisation = m::mock(Organisation::class)->makePartial();
        $this->licence->setOrganisation($this->organisation);
    }

    public function testHandleCommandWithChange()
    {
        $this->applicationCompletion->setPeopleStatus(ApplicationCompletionEntity::STATUS_NOT_STARTED);

        $this->expectStatusChange(ApplicationCompletionEntity::STATUS_INCOMPLETE);
    }

    public function testHandleCommandWithoutChange()
    {
        $this->applicationCompletion->setPeopleStatus(ApplicationCompletionEntity::STATUS_INCOMPLETE);

        $this->expectStatusUnchanged(ApplicationCompletionEntity::STATUS_INCOMPLETE);
    }

    public function testHandleCommand()
    {
        $this->applicationCompletion->setPeopleStatus(ApplicationCompletionEntity::STATUS_NOT_STARTED);

        $this->organisation->setOrganisationPersons(['foo']);

        $this->expectStatusChange(ApplicationCompletionEntity::STATUS_COMPLETE);
    }
}
