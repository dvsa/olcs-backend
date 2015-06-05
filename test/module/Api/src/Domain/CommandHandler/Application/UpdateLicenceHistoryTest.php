<?php

/**
 * Update Licence History Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Application;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\Application\UpdateLicenceHistory;
use Dvsa\Olcs\Api\Domain\Repository\Application;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\Application\UpdateLicenceHistory as Cmd;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\OtherLicence\OtherLicence;

/**
 * Update Licence History Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class UpdateLicenceHistoryTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new UpdateLicenceHistory();
        $this->mockRepo('Application', Application::class);

        parent::setUp();
    }

    public function testHandleCommandNotInProgressNoErrors()
    {
        $command = $this->getCommand();

        $otherLicences = $this->getOtherLicences();

        $application = $this->getApplication()
            ->shouldReceive('getOtherLicences')
            ->andReturn($otherLicences)
            ->shouldReceive('setPrevHasLicence')
            ->with('Y')
            ->once()
            ->shouldReceive('setPrevHadLicence')
            ->with('Y')
            ->once()
            ->shouldReceive('setPrevBeenRefused')
            ->with('Y')
            ->once()
            ->shouldReceive('setPrevBeenRevoked')
            ->with('Y')
            ->once()
            ->shouldReceive('setPrevBeenAtPi')
            ->with('Y')
            ->once()
            ->shouldReceive('setPrevBeenDisqualifiedTc')
            ->with('Y')
            ->once()
            ->shouldReceive('setPrevPurchasedAssets')
            ->with('Y')
            ->once()
            ->getMock();

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($application)
            ->once()
            ->shouldReceive('save')
            ->with($application)
            ->once()
            ->getMock();

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => ['Licence history section has been updated']
        ];

        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandNotInProgressWithErrors()
    {
        $this->setExpectedException('Dvsa\Olcs\Api\Domain\Exception\ValidationException');

        $command = $this->getCommand();

        $otherLicences = $this->getOtherLicences(true);

        $application = $this->getApplication()
            ->shouldReceive('getOtherLicences')
            ->andReturn($otherLicences)
            ->getMock();

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($application)
            ->once()
            ->getMock();

        $this->sut->handleCommand($command);
    }

    public function testHandleCommandInProgress()
    {
        $command = $this->getCommand(true);

        $application = $this->getApplication()
            ->shouldReceive('setPrevHasLicence')
            ->with('Y')
            ->once()
            ->shouldReceive('setPrevHadLicence')
            ->with('Y')
            ->once()
            ->shouldReceive('setPrevBeenRefused')
            ->with('Y')
            ->once()
            ->shouldReceive('setPrevBeenRevoked')
            ->with('Y')
            ->once()
            ->shouldReceive('setPrevBeenAtPi')
            ->with('Y')
            ->once()
            ->shouldReceive('setPrevBeenDisqualifiedTc')
            ->with('Y')
            ->once()
            ->shouldReceive('setPrevPurchasedAssets')
            ->with('Y')
            ->once()
            ->getMock();

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($application)
            ->once()
            ->shouldReceive('save')
            ->with($application)
            ->once()
            ->getMock();

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => ['Licence history section has been updated']
        ];

        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals($expected, $result->toArray());
    }

    protected function getCommand($inProgress = false)
    {
        $data = [
            'id' => 1,
            'version' => 1,
            'prevHasLicence' => 'Y',
            'prevHadLicence' => 'Y',
            'prevBeenRefused' => 'Y',
            'prevBeenRevoked' => 'Y',
            'prevBeenAtPi' => 'Y',
            'prevBeenDisqualifiedTc' => 'Y',
            'prevPurchasedAssets' => 'Y',
            'inProgress' => $inProgress
        ];

        return Cmd::create($data);
    }

    protected function getApplication()
    {
        return m::mock(ApplicationEntity::class)->makePartial();
    }

    protected function getOtherLicences($hideOne = false)
    {
        $otherLicenceCurrent = m::mock()
            ->shouldReceive('getPreviousLicenceType')
            ->andReturn(
                m::mock()
                ->shouldReceive('getId')
                ->andReturn(OtherLicence::TYPE_CURRENT)
                ->getMock()
            )
            ->getMock();

        $otherLicenceApplied = m::mock()
            ->shouldReceive('getPreviousLicenceType')
            ->andReturn(
                m::mock()
                ->shouldReceive('getId')
                ->andReturn(OtherLicence::TYPE_APPLIED)
                ->getMock()
            )
            ->getMock();

        $otherLicenceRefused = m::mock()
            ->shouldReceive('getPreviousLicenceType')
            ->andReturn(
                m::mock()
                ->shouldReceive('getId')
                ->andReturn(OtherLicence::TYPE_REFUSED)
                ->getMock()
            )
            ->getMock();

        $otherLicenceRevoked = m::mock()
            ->shouldReceive('getPreviousLicenceType')
            ->andReturn(
                m::mock()
                ->shouldReceive('getId')
                ->andReturn(OtherLicence::TYPE_REVOKED)
                ->getMock()
            )
            ->getMock();

        $otherLicencePi = m::mock()
            ->shouldReceive('getPreviousLicenceType')
            ->andReturn(
                m::mock()
                ->shouldReceive('getId')
                ->andReturn(OtherLicence::TYPE_PUBLIC_INQUIRY)
                ->getMock()
            )
            ->getMock();

        $otherLicenceDisqualified = m::mock()
            ->shouldReceive('getPreviousLicenceType')
            ->andReturn(
                m::mock()
                ->shouldReceive('getId')
                ->andReturn(OtherLicence::TYPE_DISQUALIFIED)
                ->getMock()
            )
            ->getMock();

        $otherLicenceHeld = m::mock()
            ->shouldReceive('getPreviousLicenceType')
            ->andReturn(
                m::mock()
                ->shouldReceive('getId')
                ->andReturn(OtherLicence::TYPE_HELD)
                ->getMock()
            )
            ->getMock();

        $otherLicences = [
            $otherLicenceCurrent, $otherLicenceApplied, $otherLicenceRefused, $otherLicenceRevoked,
            $otherLicencePi, $otherLicenceDisqualified
        ];
        if (!$hideOne) {
            $otherLicences[] = $otherLicenceHeld;
        }
        return $otherLicences;
    }
}
