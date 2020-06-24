<?php

/**
 * Filter Submission Sections Test
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Submission;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\Submission\FilterSubmissionSections;
use Dvsa\Olcs\Api\Domain\Repository\Submission as SubmissionRepo;
use Dvsa\Olcs\Api\Entity\Submission\Submission as SubmissionEntity;
use Dvsa\Olcs\Api\Entity\Cases\Cases as CasesEntity;
use Dvsa\Olcs\Transfer\Command\Submission\FilterSubmissionSections as Cmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Service\Submission\SubmissionGenerator;
use Dvsa\Olcs\Api\Domain\CommandHandlerManager;
use Dvsa\Olcs\Api\Domain\QueryHandlerManager;
use Dvsa\Olcs\Api\Domain\Repository\TransactionManagerInterface;
use Dvsa\Olcs\Api\Domain\RepositoryServiceManager;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use \Dvsa\Olcs\Transfer\Command\Submission\CreateSubmissionSectionComment as CommentCommand;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Rbac\PidIdentityProvider;

/**
 * Filter Submission Sections Test
 */
class FilterSubmissionSectionsTest extends CommandHandlerTestCase
{
    protected $submissionConfig = [
        'submissions' => [
            'sections' => [
                'configuration' => [
                    'introduction' => [
                        'subcategoryId' => 115,
                        'config' => [],
                        'section_type' => ['text'],
                        'allow_comments' => true,
                        'allow_attachments' => true
                    ]
                ]
            ]
        ]
    ];

    public function setUp(): void
    {
        // @todo the code is probably copy/pasted from CommandHandlerTestCase. need refactoring?
        $this->sut = new FilterSubmissionSections();
        $this->mockRepo('Submission', SubmissionRepo::class);

        $this->mockedSmServices = [
            SubmissionGenerator::class => m::mock(SubmissionGenerator::class),
            PidIdentityProvider::class => m::mock(\Dvsa\Olcs\Api\Rbac\PidIdentityProvider::class)
        ];

        // copied from parent,
        $this->repoManager = m::mock(RepositoryServiceManager::class);
        $this->queryHandler = m::mock(QueryHandlerManager::class);

        foreach ($this->repoMap as $alias => $service) {
            $this->repoManager
                ->shouldReceive('get')
                ->with($alias)
                ->andReturn($service);
        }

        $sm = m::mock(ServiceLocatorInterface::class);
        $sm->shouldReceive('get')->with('RepositoryServiceManager')->andReturn($this->repoManager);
        $sm->shouldReceive('get')->with('TransactionManager')->andReturn(m::mock(TransactionManagerInterface::class));
        $sm->shouldReceive('get')->with('QueryHandlerManager')->andReturn($this->queryHandler);
        $sm->shouldReceive('get')->with('Config')->andReturn($this->submissionConfig);

        foreach ($this->mockedSmServices as $serviceName => $service) {
            $sm->shouldReceive('get')->with($serviceName)->andReturn($service);
        }

        $this->commandHandler = m::mock(CommandHandlerManager::class);
        $this->commandHandler
            ->shouldReceive('getServiceLocator')
            ->andReturn($sm);

        $this->sut->createService($this->commandHandler);

        $this->sideEffects = [];
        $this->commands = [];

        $this->initReferences();
    }

    protected function initReferences()
    {
        $this->refData = [
            'case-summary',
            'submission_type_o_mlh_otc'
        ];

        $this->references = [
            SubmissionEntity::class => [
                1 => m::mock(SubmissionEntity::class)
            ],
            CasesEntity::class => [
                24 => m::mock(CasesEntity::class)
            ]
        ];

        parent::initReferences();
    }

    public function testHandleCommandSingleSection()
    {
        $data = [
            'id' => 122,
            'version' => 3,
            'case' => '24',
            'submissionType'=> 'submission_type_o_mlh_otc',
            'section' => 'operating-centres',
            'rowsToFilter'=> [72]
        ];

        /** @var SubmissionEntity $submissionMock */
        $submissionMock = $this->getMockSubmission();

        $command = Cmd::create($data);

        $this->repoMap['Submission']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT, 3)
            ->andReturn($submissionMock)
            ->shouldReceive('save')
            ->with($submissionMock);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'submission' => 111,
            ],
            'messages' => [
                'Submission filtered successfully'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());

        // assert data has been filtered
        $filterData = [
            'data' => [
                'tables' => [
                    'operating-centres' => [
                        1 => [
                            'id' => 16
                        ]
                    ]
                ]
            ]
        ];
        $newDataSnapshot = json_decode($submissionMock->getDataSnapshot(), true);

        $this->assertEquals($filterData, $newDataSnapshot['operating-centres']);
    }

    public function testHandleCommandSubSection()
    {
        $data = [
            'id' => 122,
            'version' => 3,
            'case' => '24',
            'submissionType'=> 'submission_type_o_mlh_otc',
            'section' => 'conditions-and-undertakings',
            'subSection' => 'conditions',
            'rowsToFilter'=> [3]
        ];

        /** @var SubmissionEntity $submissionMock */
        $submissionMock = $this->getMockSubmission();

        $command = Cmd::create($data);

        $this->repoMap['Submission']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT, 3)
            ->andReturn($submissionMock)
            ->shouldReceive('save')
            ->with($submissionMock);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'submission' => 111,
            ],
            'messages' => [
                'Submission filtered successfully'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());

        // assert data has been filtered
        $filterData = [
            'data' => [
                'tables' => [
                    'undertakings' => [
                        0 => [
                            'id' => 5
                        ]
                    ],
                    'conditions' => [
                        0 => [
                            'id' => 10
                        ]
                    ]
                ]
            ]
        ];
        $newDataSnapshot = json_decode($submissionMock->getDataSnapshot(), true);

        $this->assertEquals($filterData, $newDataSnapshot['conditions-and-undertakings']);

        // check that conditions have been updated
        $this->assertEquals(
            $newDataSnapshot['conditions-and-undertakings']['data']['tables']['conditions'],
            $filterData['data']['tables']['conditions']
        );
    }

    private function getMockSubmission()
    {
        $mockDataSnapshot = '
{
    "operating-centres": {
        "data": {
            "tables": {
                "operating-centres": [
                    {
                        "id": 72
                    },
                    {
                        "id": 16
                    }
                ]
            }
        }
    },
    "conditions-and-undertakings": {
        "data": {
            "tables": {
                "undertakings": [
                    {
                        "id": 5
                    }
                ],
                "conditions": [
                    {
                        "id": 10
                    },
                    {
                        "id": 3
                    }
                ]
            }
        }
    }
}';
        /** @var SubmissionEntity $submissionMock */
        $submissionMock = m::mock(SubmissionEntity::class)->makePartial();
        $submissionMock->setId(111);
        $submissionMock->setDataSnapshot($mockDataSnapshot);

        return $submissionMock;
    }
}
