<?php

/**
 * Create Psv Discs Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Variation;

use Dvsa\Olcs\Api\Domain\Command\Application\UpdateApplicationCompletion;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Mockery as m;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Repository\Application;
use Dvsa\Olcs\Api\Domain\CommandHandler\Variation\CreatePsvDiscs;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Transfer\Command\Variation\CreatePsvDiscs as Cmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\Licence\CreatePsvDiscs as LicenceCreatePsvDiscs;

/**
 * Create Psv Discs Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CreatePsvDiscsTest extends CommandHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new CreatePsvDiscs();
        $this->mockRepo('Application', Application::class);

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
        $data = [
            'application' => 111,
            'amount' => 2,
            'isCopy' => 'Y'
        ];

        $command = Cmd::create($data);

        $licence = m::mock(LicenceEntity::class)->makePartial();
        $licence->setId(222);

        $application = m::mock(ApplicationEntity::class)->makePartial();
        $application->setLicence($licence);

        $this->repoMap['Application']->shouldReceive('fetchById')
            ->with(111)
            ->andReturn($application);

        $expectedData = [
            'licence' => 222,
            'amount' => 2,
            'isCopy' => 'Y'
        ];
        $result1 = new Result();
        $result1->addMessage('Foo');
        $this->expectedSideEffect(LicenceCreatePsvDiscs::class, $expectedData, $result1);

        $expectedData = [
            'id' => 111,
            'section' => 'discs'
        ];
        $result2 = new Result();
        $result2->addMessage('Bar');
        $this->expectedSideEffect(UpdateApplicationCompletion::class, $expectedData, $result2);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'Foo',
                'Bar'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
