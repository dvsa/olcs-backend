<?php

namespace Dvsa\OlcsTest\Cli\Domain\CommandHandler;

use Doctrine\DBAL\Connection;
use Dvsa\Olcs\Api\Domain\Command\Document\GenerateAndStoreWithMultipleAddresses;
use Dvsa\Olcs\Api\Domain\Exception\RuntimeException;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Cli\Domain\CommandHandler\LastTmLetter;
use Dvsa\Olcs\Transfer\Command\Document\PrintLetter;
use Dvsa\Olcs\Transfer\Command\Task\CreateTask;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Tm\TransportManagerLicence as TmlEntity;
use Dvsa\Olcs\Api\Entity\User\User as UserEntity;
use Dvsa\Olcs\Api\Entity\Doc\Document as DocumentEntity;

/**
 * Create VI Extract Files Test
 */
class LastTmLetterTest extends CommandHandlerTestCase
{

    public function setUp()
    {

        $this->sut = new LastTmLetter();

        $this->mockRepo('Licence', Repository\Licence::class);
        $this->mockRepo('User', Repository\User::class);
        $this->mockRepo('Document', Repository\Document::class);
        $this->mockRepo('DocTemplate', Repository\DocTemplate::class);

        parent::setUp();
    }

    public function dpHandleCommand() {
        return [
            'no_licences_with_removed_tm' => [
                'data' => [
                    'licence' => []
                ],
                'expect' => [
                    'id' => [],
                    'messages' => []
                ]
            ],
            'licence_with_removed_tm_allow_email_gb_gv' => [
                'data' => [
                    'licence' => [
                        'id' => 1,
                        'licNo' => 'AB123',
                        'isNi' => false,
                        'isPsv' => false,
                        'organisation' => [
                            'allowEmail' => 'Y'
                        ]
                    ],
                    'user' => [
                        'contactDetails ' => [
                            'address' => '12 Food Road'
                        ]
                    ],
                    'sideEffectResults' => [
                        'GenerateAndStoreWithMultipleAddresses' => [
                            'ids' => [
                                'documents' => [
                                    '123' => [
                                        'metadata' => json_encode(['details' => [
                                            'category' => Category::CATEGORY_TRANSPORT_MANAGER,
                                            'documentSubCategory' => Category::DOC_SUB_CATEGORY_TRANSPORT_MANAGER_CORRESPONDENCE,
                                            'documentTemplate' => 1,
                                            'allowEmail' => 'Y',
                                            'sendToAddress' => 'correspondenceAddress'
                                        ]])
                                    ],
                                    '234'=> [
                                        'metadata' => json_encode(['details' => [
                                            'category' => Category::CATEGORY_TRANSPORT_MANAGER,
                                            'documentSubCategory' => Category::DOC_SUB_CATEGORY_TRANSPORT_MANAGER_CORRESPONDENCE,
                                            'documentTemplate' => 1,
                                            'allowEmail' => 'Y',
                                            'sendToAddress' => 'establishmentAddress'
                                        ]])
                                    ]
                                ]
                            ]
                        ],
                        'CreateTask' => [
                            'ids' => [
                                'assignedToUser' => 111
                            ]

                        ]
                    ]

                ],
                'expect' => [
                    'id' => [
                        'assignedToUser' => 111,
                        'documents' => [123, 234]
                    ],
                    'messages' => [
                        "Document id '123', queued for print",
                        "Correspondence record created",
                        "Email sent",
                        "Document id '234', queued for print"
                    ]
                ]
            ],
            'licence_with_removed_tm_allow_email_ni_gv' => [
                'data' => [
                    'licence' => [
                        'id' => 1,
                        'licNo' => 'AB123',
                        'isNi' => true,
                        'isPsv' => false,
                        'organisation' => [
                            'allowEmail' => 'Y'
                        ]
                    ],
                    'user' => [
                        'contactDetails ' => [
                            'address' => '12 Food Road'
                        ]
                    ],
                    'sideEffectResults' => [
                        'GenerateAndStoreWithMultipleAddresses' => [
                            'ids' => [
                                'documents' => [
                                    '123' => [
                                        'metadata' => json_encode(['details' => [
                                            'category' => Category::CATEGORY_TRANSPORT_MANAGER,
                                            'documentSubCategory' => Category::DOC_SUB_CATEGORY_TRANSPORT_MANAGER_CORRESPONDENCE,
                                            'documentTemplate' => 1,
                                            'allowEmail' => 'Y',
                                            'sendToAddress' => 'correspondenceAddress'
                                        ]])
                                    ],
                                    '234'=> [
                                        'metadata' => json_encode(['details' => [
                                            'category' => Category::CATEGORY_TRANSPORT_MANAGER,
                                            'documentSubCategory' => Category::DOC_SUB_CATEGORY_TRANSPORT_MANAGER_CORRESPONDENCE,
                                            'documentTemplate' => 1,
                                            'allowEmail' => 'Y',
                                            'sendToAddress' => 'establishmentAddress'
                                        ]])
                                    ]
                                ]
                            ]
                        ],
                        'CreateTask' => [
                            'ids' => [
                                'assignedToUser' => 111
                            ]

                        ]
                    ]

                ],
                'expect' => [
                    'id' => [
                        'assignedToUser' => 111,
                        'documents' => [123, 234]
                    ],
                    'messages' => [
                        "Document id '123', queued for print",
                        "Correspondence record created",
                        "Email sent",
                        "Document id '234', queued for print"
                    ]
                ]
            ],
            'licence_with_removed_tm_not_allow_email_gb_psv' => [
                'data' => [
                    'licence' => [
                        'id' => 1,
                        'licNo' => 'AB123',
                        'isNi' => false,
                        'isPsv' => true,
                        'organisation' => [
                            'allowEmail' => 'N'
                        ]
                    ],
                    'user' => [
                        'contactDetails ' => [
                            'address' => '12 Food Road'
                        ]
                    ],
                    'sideEffectResults' => [
                        'GenerateAndStoreWithMultipleAddresses' => [
                            'ids' => [
                                'documents' => [
                                    '123' => [
                                        'metadata' => json_encode(['details' => [
                                            'category' => Category::CATEGORY_TRANSPORT_MANAGER,
                                            'documentSubCategory' => Category::DOC_SUB_CATEGORY_TRANSPORT_MANAGER_CORRESPONDENCE,
                                            'documentTemplate' => 1,
                                            'allowEmail' => 'N',
                                            'sendToAddress' => 'correspondenceAddress'
                                        ]])
                                    ],
                                    '234'=> [
                                        'metadata' => json_encode(['details' => [
                                            'category' => Category::CATEGORY_TRANSPORT_MANAGER,
                                            'documentSubCategory' => Category::DOC_SUB_CATEGORY_TRANSPORT_MANAGER_CORRESPONDENCE,
                                            'documentTemplate' => 1,
                                            'allowEmail' => 'N',
                                            'sendToAddress' => 'establishmentAddress'
                                        ]])
                                    ]
                                ]
                            ]
                        ],
                        'CreateTask' => [
                            'ids' => [
                                'assignedToUser' => 111
                            ]

                        ]
                    ]

                ],
                'expect' => [
                    'id' => [
                        'assignedToUser' => 111,
                        'documents' => [123, 234]
                    ],
                    'messages' => [
                        "Document id '123', queued for print",
                        "Document id '234', queued for print"
                    ]
                ]
            ]
        ];
    }

    /**
     * @dataProvider dpHandleCommand
     */
    public function testHandleCommand($dataProvider, $expectedResult)
    {

        $licenceRepo = $this->repoMap['Licence'];
        $licence = $this->mockLicence($dataProvider);

        $eligibleLicences = $licence === null ? [] : [$licence];

        $licenceRepo->shouldReceive('fetchForLastTmAutoLetter')->andReturn($eligibleLicences);

        if(!empty($eligibleLicences)) {
            $userRepo = $this->repoMap['User'];
            $user = $this->mockUser();
            $userRepo->shouldReceive('fetchById')
                ->with($dataProvider['sideEffectResults']['CreateTask']['ids']['assignedToUser'])
                ->andReturn($user);

            $documentsData = $dataProvider['sideEffectResults']['GenerateAndStoreWithMultipleAddresses']['ids']['documents'];

            $generateAndStoreMultipleAddressesResult = $this->getGenerateAndStoreMultipleAddressesResult($documentsData);

            $this->expectedSideEffect(
                GenerateAndStoreWithMultipleAddresses::class,
                [],
                $generateAndStoreMultipleAddressesResult
            );

            $createTaskResult = $this->getCreateTaskResult($dataProvider);
            $this->expectedSideEffect(CreateTask::class, [], $createTaskResult);

            $documents = [];
            $documentRepo = $this->repoMap['Document'];
            foreach ($documentsData as $id => $data) {
                $printLetterResult = $this->getPrintLetterResult($id);
                $this->expectedSideEffect(PrintLetter::class, [], $printLetterResult);

                $metadata = json_decode($data['metadata'], true);
                if($metadata['details']['sendToAddress'] === 'correspondenceAddress' &&
                    $metadata['details']['allowEmail'] === 'Y' ) {
                    $printLetterEmailResult = $this->getPrintLetterEmailResult();
                    $this->expectedSideEffect(PrintLetter::class, [], $printLetterEmailResult);
                }

                $documents[$id] = m::mock(DocumentEntity::class);
                $documents[$id]->shouldReceive('getMetadata')->andReturn($data['metadata']);

                $documentRepo->shouldReceive('fetchById')->with($id)->once()->andReturn($documents[$id]);
            }
        }

        $response = $this->sut->handleCommand(\Dvsa\Olcs\Cli\Domain\Command\LastTmLetter::create([]));

        $this->assertEquals($expectedResult, $response->toArray());
    }

    private function getGenerateAndStoreMultipleAddressesResult($documents)
    {
        $result = new Result();

        foreach ($documents as $id => $data) {
            $result->addId('documents', $id, true);
        }

        return $result;
    }

    private function getCreateTaskResult($dataProvider)
    {
        $result = new Result();
        $result->addId('assignedToUser', $dataProvider['sideEffectResults']['CreateTask']['ids']['assignedToUser']);

        return $result;
    }

    private function getPrintLetterResult($documentId)
    {
        $result = new Result();
        $result->addMessage("Document id '$documentId', queued for print");
        return $result;
    }

    private function getPrintLetterEmailResult()
    {
        $result = new Result();
        $result->addMessage('Correspondence record created');
        $result->addMessage('Email sent');

        return $result;
    }

    private function mockUser()
    {
        $caseworkerDetailsBundle = [
            'contactDetails' => [
                'address',
                'phoneContacts' => [
                    'phoneContactType'
                ],
                'person'
            ],
            'team' => [
                'trafficArea' => [
                    'contactDetails' => [
                        'address'
                    ]
                ]
            ]
        ];

        $caseworkerNameBundle = [
            'contactDetails' => [
                'person'
            ]
        ];


        $user = m::mock(UserEntity::class);
        $user->shouldReceive('serialize')
            ->with($caseworkerDetailsBundle)
            ->once()
            ->andReturn([]);
        $user->shouldReceive('serialize')
            ->with($caseworkerNameBundle)
            ->once()
            ->andReturn([]);

        return $user;
    }

    private function mockLicence($dataProvider)
    {
        if(empty($dataProvider['licence'])) {
            return null;
        }


        $licence = m::mock(LicenceEntity::class);

        $licenceBundle = [
            'trafficArea',
        ];

        $licence->shouldReceive('getId')->andReturn($dataProvider['licence']['id']);
        $licence->shouldReceive('getLicNo')->andReturn($dataProvider['licence']['licNo']);
        $licence->shouldReceive('isNi')->andReturn($dataProvider['licence']['isNi']);
        $licence->shouldReceive('isPsv')->andReturn($dataProvider['licence']['isPsv']);
        $licence->shouldReceive('getOrganisation->getAllowEmail')
            ->andReturn($dataProvider['licence']['organisation']['allowEmail']);
        $licence->shouldReceive('serialize')->with($licenceBundle)->andReturn([]);

        return $licence;
    }

}
