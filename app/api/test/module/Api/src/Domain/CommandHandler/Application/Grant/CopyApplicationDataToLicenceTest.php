<?php

/**
 * Copy Application Data To Licence Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Application\Grant;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Application\Grant\CopyApplicationDataToLicence;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Transfer\Command\Licence\PrintLicence;
use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Command\Application\Grant\CopyApplicationDataToLicence as CopyApplicationDataToLicenceCmd;

/**
 * Copy Application Data To Licence Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CopyApplicationDataToLicenceTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CopyApplicationDataToLicence();
        $this->mockRepo('Application', \Dvsa\Olcs\Api\Domain\Repository\Application::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            Licence::LICENCE_STATUS_VALID
        ];

        $this->references = [];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $data = [
            'id' => 111
        ];

        $command = CopyApplicationDataToLicenceCmd::create($data);

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();

        /** @var ApplicationEntity $application */
        $application = m::mock(ApplicationEntity::class)->makePartial();
        $application->setLicence($licence);

        $licence->shouldReceive('copyInformationFromApplication')
            ->once()
            ->with($application);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($application)
            ->shouldReceive('save')
            ->once()
            ->with($application);

        $result = $this->sut->handleCommand($command);

        $this->assertInstanceOf(Result::class, $result);

        $now = new DateTime();
        $review = new DateTime('+5 years');
        $dom = $now->format('j');
        $expiry = new DateTime('+5 years -' . $dom . ' days');

        $this->assertEquals($now->format('Y-m-d'), $licence->getInForceDate()->format('Y-m-d'));
        $this->assertEquals($review->format('Y-m-d'), $licence->getReviewDate()->format('Y-m-d'));
        $this->assertEquals($expiry->format('Y-m-d'), $licence->getExpiryDate()->format('Y-m-d'));
        $this->assertEquals($expiry->format('Y-m-d'), $licence->getFeeDate()->format('Y-m-d'));
    }
}
