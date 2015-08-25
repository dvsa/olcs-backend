<?php

/**
 * Delete Operating Centres Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Variation;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Domain\Command\Application\UpdateApplicationCompletion;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Exception\BadRequestException;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\Application\ApplicationOperatingCentre;
use Dvsa\Olcs\Api\Entity\Licence\LicenceOperatingCentre;
use Dvsa\Olcs\Api\Entity\OperatingCentre\OperatingCentre;
use Mockery as m;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Domain\CommandHandler\Variation\DeleteOperatingCentre;
use Dvsa\Olcs\Transfer\Command\Variation\DeleteOperatingCentre as Cmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\Licence\DeleteOperatingCentres as LicenceDeleteOperatingCentres;

/**
 * Delete Operating Centres Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class DeleteOperatingCentreTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new DeleteOperatingCentre();
        $this->mockRepo('Application', Repository\Application::class);
        $this->mockRepo('ApplicationOperatingCentre', Repository\ApplicationOperatingCentre::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [];

        $this->references = [
            ApplicationOperatingCentre::class => [
                22 => m::mock(ApplicationOperatingCentre::class)
            ],
            LicenceOperatingCentre::class => [
                22 => m::mock(LicenceOperatingCentre::class)
            ]
        ];

        parent::initReferences();
    }

    public function testHandleCommandAppInvalid()
    {
        $data = [
            'id' => 'A22',
            'application' => 111
        ];
        $command = Cmd::create($data);

        /** @var Application $application */
        $application = m::mock(Application::class)->makePartial();

        $this->repoMap['Application']->shouldReceive('fetchById')
            ->with(111)
            ->andReturn($application);

        $this->references[ApplicationOperatingCentre::class][22]->setAction('D');

        $this->setExpectedException(BadRequestException::class);

        $this->sut->handleCommand($command);
    }

    public function testHandleCommandLicInvalid()
    {
        $data = [
            'id' => 'L22',
            'application' => 111
        ];
        $command = Cmd::create($data);

        $aoc = m::mock();

        $aocs = new ArrayCollection();
        $aocs->add($aoc);

        /** @var Application $application */
        $application = m::mock(Application::class)->makePartial();
        $application->shouldReceive('getOperatingCentres->matching')
            ->andReturn($aocs);

        $this->repoMap['Application']->shouldReceive('fetchById')
            ->with(111)
            ->andReturn($application);

        /** @var OperatingCentre $oc */
        $oc = m::mock(OperatingCentre::class)->makePartial();

        $this->references[LicenceOperatingCentre::class][22]->setOperatingCentre($oc);

        $this->setExpectedException(BadRequestException::class);

        $this->sut->handleCommand($command);
    }

    public function testHandleCommandAppValid()
    {
        $data = [
            'id' => 'A22',
            'application' => 111
        ];
        $command = Cmd::create($data);

        /** @var Application $application */
        $application = m::mock(Application::class)->makePartial();
        $application->setId(111);

        $this->repoMap['Application']->shouldReceive('fetchById')
            ->with(111)
            ->andReturn($application);

        $this->references[ApplicationOperatingCentre::class][22]->setAction('A');

        $this->repoMap['ApplicationOperatingCentre']->shouldReceive('delete')
            ->once()
            ->with($this->references[ApplicationOperatingCentre::class][22]);

        $data = [
            'id' => 111,
            'section' => 'operatingCentres'
        ];
        $result = new Result();
        $result->addMessage('UpdateApplicationCompletion');
        $this->expectedSideEffect(UpdateApplicationCompletion::class, $data, $result);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'Removed application operating centre delta record',
                'UpdateApplicationCompletion'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandAppCannotDelete()
    {
        $data = [
            'id' => 'A22',
            'application' => 111
        ];
        $command = Cmd::create($data);

        /** @var Application $application */
        $application = m::mock(Application::class)->makePartial();
        $application->setId(111);

        $this->repoMap['Application']->shouldReceive('fetchById')
            ->with(111)
            ->andReturn($application);

        $this->references[ApplicationOperatingCentre::class][22]->setAction('A');
        $this->references[ApplicationOperatingCentre::class][22]->shouldReceive('checkCanDelete')->with()->once()
            ->andReturn(['ERROR' => 'Foo']);

        $this->setExpectedException(BadRequestException::class);
        $this->sut->handleCommand($command);
    }

    public function testHandleCommandLicValid()
    {
        $data = [
            'id' => 'L22',
            'application' => 111
        ];
        $command = Cmd::create($data);

        $aocs = new ArrayCollection();

        /** @var Application $application */
        $application = m::mock(Application::class)->makePartial();
        $application->setId(111);
        $application->shouldReceive('getOperatingCentres->matching')
            ->andReturn($aocs);

        $this->repoMap['Application']->shouldReceive('fetchById')
            ->with(111)
            ->andReturn($application);

        /** @var OperatingCentre $oc */
        $oc = m::mock(OperatingCentre::class)->makePartial();

        $this->references[LicenceOperatingCentre::class][22]->setOperatingCentre($oc);

        $this->repoMap['ApplicationOperatingCentre']->shouldReceive('save')
            ->once()
            ->with(m::type(ApplicationOperatingCentre::class));

        $data = [
            'id' => 111,
            'section' => 'operatingCentres'
        ];
        $result = new Result();
        $result->addMessage('UpdateApplicationCompletion');
        $this->expectedSideEffect(UpdateApplicationCompletion::class, $data, $result);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'Created application operating centre delta record',
                'UpdateApplicationCompletion'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandCannotDelete()
    {
        $data = [
            'id' => 'L22',
            'application' => 111
        ];
        $command = Cmd::create($data);

        $aocs = new ArrayCollection();

        /** @var Application $application */
        $application = m::mock(Application::class)->makePartial();
        $application->setId(111);
        $application->shouldReceive('getOperatingCentres->matching')
            ->andReturn($aocs);

        $this->repoMap['Application']->shouldReceive('fetchById')
            ->with(111)
            ->andReturn($application);

        /** @var OperatingCentre $oc */
        $oc = m::mock(OperatingCentre::class)->makePartial();

        $this->references[LicenceOperatingCentre::class][22]->setOperatingCentre($oc);
        $this->references[LicenceOperatingCentre::class][22]->shouldReceive('checkCanDelete')->with()->once()
            ->andReturn(['ERROR' => 'Foo']);

        $this->setExpectedException(BadRequestException::class);

        $this->sut->handleCommand($command);
    }
}
