<?php

/**
 * Update Financial Evidence Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Application;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\Application\UpdateFinancialEvidence;
use Dvsa\Olcs\Api\Domain\Repository\Application;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\Application\UpdateFinancialEvidence as Cmd;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;

/**
 * Update Financial Evidence Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class UpdateFinancialEvidenceTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new UpdateFinancialEvidence();
        $this->mockRepo('Application', Application::class);

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $command = $this->getCommand();

        $application = $this->getApplication()
            ->shouldReceive('setFinancialEvidenceUploaded')
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
            'messages' => ['Financial evidence section has been updated']
        ];

        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals($expected, $result->toArray());
    }

    protected function getCommand()
    {
        $data = [
            'id' => 1,
            'version' => 1,
            'financialEvidenceUploaded' => 'Y',
        ];

        return Cmd::create($data);
    }

    protected function getApplication()
    {
        return m::mock(ApplicationEntity::class)->makePartial();
    }
}
