<?php

/**
 * Delete Licence History Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Application;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\OtherLicence\DeleteOtherLicence;
use Dvsa\Olcs\Api\Domain\Repository\OtherLicenceRepo;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\OtherLicence\DeleteOtherLicence as Cmd;
use Dvsa\Olcs\Api\Entity\OtherLicence\OtherLicence as OtherLicenceEntity;

/**
 * Update Other Licence Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class DeleteOtherLicenceTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new DeleteOtherLicence();
        $this->mockRepo('OtherLicence', OtherLicenceRepo::class);

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $command = $this->getCommand();

        $otherLicence = $this->getOtherLicence();

        $this->repoMap['OtherLicence']->shouldReceive('fetchById')
            ->andReturn($otherLicence)
            ->times(3)
            ->shouldReceive('delete')
            ->with($otherLicence)
            ->times(3)
            ->getMock();

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'otherLicence1' => 1,
                'otherLicence2' => 2,
                'otherLicence3' => 3
            ],
            'messages' => [
                'Other licence removed',
                'Other licence removed',
                'Other licence removed'
            ]
        ];

        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals($expected, $result->toArray());
    }

    protected function getCommand()
    {
        $data = [
            'ids' => [1, 2, 3]
        ];

        return Cmd::create($data);
    }

    protected function getOtherLicence()
    {
        return m::mock(OtherLicenceEntity::class)->makePartial();
    }
}
