<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Bus\Ebsr;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Command\Bus\Ebsr\CreateSubmission as CreateSubmissionCmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\Bus\Ebsr\CreateSubmission;
use Dvsa\Olcs\Api\Entity\Doc\Document as DocumentEntity;
use Dvsa\Olcs\Api\Entity\Ebsr\EbsrSubmission as EbsrSubmissionEntity;
use Dvsa\Olcs\Api\Domain\Repository\EbsrSubmission as EbsrSubmissionRepo;
use ZfcRbac\Service\AuthorizationService;
use ZfcRbac\Identity\IdentityInterface;

/**
 * Create Submission Test
 */
class CreateSubmissionTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CreateSubmission();
        $this->mockRepo('EbsrSubmission', EbsrSubmissionRepo::class);

        $this->mockedSmServices = [
            AuthorizationService::class => m::mock(AuthorizationService::class)
        ];

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->references = [
            DocumentEntity::class => [
                111 => m::mock(DocumentEntity::class)
            ]
        ];

        $this->refData = [
            EbsrSubmissionEntity::SUBMITTING_STATUS,
            EbsrSubmissionEntity::UNKNOWN_SUBMISSION_TYPE,
        ];

        parent::initReferences();
    }

    /**
     * Tests handleCommand
     */
    public function testHandleCommand()
    {
        $command = CreateSubmissionCmd::create(
            [
                'document' => 111,
            ]
        );

        $organisation = m::mock(Organisation::class);

        $identity = m::mock(IdentityInterface::class);
        $identity->shouldReceive('getUser->getOrganisationUsers->isEmpty')->once()->andReturn(false);
        $identity->shouldReceive('getUser->getRelatedOrganisation')->once()->andReturn($organisation);

        $this->mockedSmServices[AuthorizationService::class]
            ->shouldReceive('getIdentity')
            ->once()
            ->andReturn($identity);

        $this->repoMap['EbsrSubmission']->shouldReceive('save')
            ->with(m::type(EbsrSubmissionEntity::class))
            ->once();

        $result = $this->sut->handleCommand($command);

        $this->assertInstanceOf('Dvsa\Olcs\Api\Domain\Command\Result', $result);
    }
}
