<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\ContinuationDetail;

use Dvsa\Olcs\Api\Domain\Command\ContinuationDetail\ProcessReminder as Command;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\ContinuationDetail\ProcessReminder as CommandHandler;
use Dvsa\Olcs\Api\Service\Document\DocumentGenerator as DocGenerator;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\ContinuationDetail as ContinuationDetailRepo;
use Dvsa\Olcs\Api\Domain\Repository\Document as DocumentRepo;
use Mockery as m;
use Dvsa\Olcs\Api\Entity\User\User;
use ZfcRbac\Service\AuthorizationService;
use Dvsa\Olcs\Api\Entity\System\Category as CategoryEntity;
use Dvsa\Olcs\Api\Entity\Licence\ContinuationDetail as ContinuationDetailEntity;

/**
 * Process reminder test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class ProcessReminderTest extends CommandHandlerTestCase
{
    protected $user;

    public function setUp()
    {
        $this->sut = new CommandHandler();
        $this->mockRepo('ContinuationDetail', ContinuationDetailRepo::class);
        $this->mockRepo('Document', DocumentRepo::class);

        $this->mockedSmServices = [
            AuthorizationService::class => m::mock(AuthorizationService::class),
            'DocumentGenerator' => m::mock(DocGenerator::class)
        ];
        $this->mockAuthService();

        parent::setUp();
    }

    /**
     * @dataProvider detailsProvider
     */
    public function testHandleCommand($isGoods, $template)
    {
        $command = Command::create(['id' => 1]);

        $mockLicence = m::mock()
            ->shouldReceive('getId')
            ->andReturn(3)
            ->twice()
            ->shouldReceive('isGoods')
            ->andReturn($isGoods)
            ->once()
            ->getMock();

        $mockContinuationDetail = m::mock(ContinuationDetailEntity::class)
            ->shouldReceive('getLicence')
            ->andReturn($mockLicence)
            ->times(3)
            ->getMock();

        $this->repoMap['ContinuationDetail']
            ->shouldReceive('fetchWithLicence')
            ->with(1)
            ->andReturn($mockContinuationDetail)
            ->getMock();

        $mockStoredFile = m::mock(\Dvsa\Olcs\Api\Service\File\File::class)->makePartial();
        $mockStoredFile->setIdentifier(1)->setSize(12345);
        $this->mockedSmServices['DocumentGenerator']
            ->shouldReceive('generateAndStore')
            ->with(
                $template,
                [
                    'licence' => 3,
                    'user' => $this->user
                ]
            )
            ->once()
            ->andReturn($mockStoredFile);

        $docResult = new Result();
        $docResult->addId('document', 101)->addMessage('Document dispatched');
        $this->expectedSideEffect(
            \Dvsa\Olcs\Api\Domain\Command\Document\DispatchDocument::class,
            [
                'identifier' => 1,
                'size' => 12345,
                'description' => 'Checklist reminder',
                'filename' => $template . '.rtf',
                'licence' => 3,
                'category' => CategoryEntity::CATEGORY_LICENSING,
                'subCategory' => CategoryEntity::DOC_SUB_CATEGORY_CONTINUATIONS_AND_RENEWALS_LICENCE,
                'isExternal'  => false,
                'isScan' => false,
            ],
            $docResult
        );

        $result = $this->sut->handleCommand($command);

        $expected = [
            'messages' => [
                'Document dispatched',
                'Continuation checklist reminder letter generated'
            ],
            'ids' => [
                'document' => 101
            ]
        ];

        $this->assertEquals($expected['messages'], $result->getMessages());
        $this->assertEquals(['document' => 101], $result->getIds());
    }

    public function detailsProvider()
    {
        return [
            [true, 'LIC_CONTD_NO_CHECKLIST_GV'],
            [false, 'LIC_CONTD_NO_CHECKLIST_PSV']
        ];
    }

    protected function mockAuthService()
    {
        $mockUser = m::mock(User::class)->makePartial();
        $mockUser->setId(1);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('getIdentity->getUser')
            ->andReturn($mockUser);

        $this->user = $mockUser;
    }
}
