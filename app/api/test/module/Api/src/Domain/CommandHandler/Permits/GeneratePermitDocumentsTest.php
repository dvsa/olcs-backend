<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Permits;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\IrhpPermit\GenerateCoverLetterDocument;
use Dvsa\Olcs\Api\Domain\Command\IrhpPermit\GeneratePermitDocument;
use Dvsa\Olcs\Api\Domain\Command\Permits\GeneratePermitDocuments as Cmd;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Permits\GeneratePermitDocuments as Sut;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermit as IrhpPermitRepo;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermit as IrhpPermitEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication as IrhpPermitApplicationEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType as IrhpPermitTypeEntity;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

/**
 * GeneratePermitDocumentsTest
 */
class GeneratePermitDocumentsTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new Sut();
        $this->mockRepo('IrhpPermit', IrhpPermitRepo::class);

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $irhpPermitId1 = 1;
        $irhpPermitId2 = 2;
        $irhpPermitId3 = 3;

        $command = Cmd::Create(
            [
                'ids' => [
                    $irhpPermitId1, $irhpPermitId2, $irhpPermitId3
                ]
            ]
        );

        $irhpPermitType = m::mock(IrhpPermitTypeEntity::class);
        $irhpPermitType->shouldReceive('isBilateral')
            ->andReturn(false)
            ->shouldReceive('isMultilateral')
            ->andReturn(false)
            ->shouldReceive('isEcmtShortTerm')
            ->andReturn(false)
            ->shouldReceive('isEcmtRemoval')
            ->andReturn(false);

        $irhpPermitApplication1 = m::mock(IrhpPermitApplicationEntity::class);
        $irhpPermitApplication1->shouldReceive('getIrhpPermitWindow->getIrhpPermitStock->getIrhpPermitType')
            ->andReturn($irhpPermitType);

        $irhpPermitApplication2 = m::mock(IrhpPermitApplicationEntity::class);
        $irhpPermitApplication2->shouldReceive('getIrhpPermitWindow->getIrhpPermitStock->getIrhpPermitType')
            ->andReturn($irhpPermitType);

        $irhpPermitApplication3 = m::mock(IrhpPermitApplicationEntity::class);
        $irhpPermitApplication3->shouldReceive('getIrhpPermitWindow->getIrhpPermitStock->getIrhpPermitType')
            ->andReturn($irhpPermitType);

        $irhpPermit1 = m::mock(IrhpPermitEntity::class);
        $irhpPermit1->shouldReceive('getIrhpPermitApplication')->andReturn($irhpPermitApplication1);
        $irhpPermit1->shouldReceive('getId')->andReturn($irhpPermitId1);

        $irhpPermit2 = m::mock(IrhpPermitEntity::class);
        $irhpPermit2->shouldReceive('getIrhpPermitApplication')->andReturn($irhpPermitApplication2);
        $irhpPermit2->shouldReceive('getId')->andReturn($irhpPermitId2);

        $irhpPermit3 = m::mock(IrhpPermitEntity::class);
        $irhpPermit3->shouldReceive('getIrhpPermitApplication')->andReturn($irhpPermitApplication3);
        $irhpPermit3->shouldReceive('getId')->andReturn($irhpPermitId3);

        $this->repoMap['IrhpPermit']->shouldReceive('fetchById')
            ->with($irhpPermitId1, Query::HYDRATE_OBJECT)
            ->andReturn($irhpPermit1)
            ->shouldReceive('fetchById')
            ->with($irhpPermitId2, Query::HYDRATE_OBJECT)
            ->andReturn($irhpPermit2)
            ->shouldReceive('fetchById')
            ->with($irhpPermitId3, Query::HYDRATE_OBJECT)
            ->andReturn($irhpPermit3);

        $this->expectedSideEffect(
            GenerateCoverLetterDocument::class,
            [
                'irhpPermit' => $irhpPermitId1,
            ],
            (new Result())->addId('letters', 101)->addMessage('Cover letter #1 generated')
        );

        $this->expectedSideEffect(
            GenerateCoverLetterDocument::class,
            [
                'irhpPermit' => $irhpPermitId2,
            ],
            (new Result())->addId('letters', 102)->addMessage('Cover letter #2 generated')
        );

        $this->expectedSideEffect(
            GenerateCoverLetterDocument::class,
            [
                'irhpPermit' => $irhpPermitId3,
            ],
            (new Result())->addId('letters', 103)->addMessage('Cover letter #3 generated')
        );

        $this->expectedSideEffect(
            GeneratePermitDocument::class,
            [
                'irhpPermit' => $irhpPermitId1,
            ],
            (new Result())->addId('permits', 201)->addMessage('Permit #1 generated')
        );

        $this->expectedSideEffect(
            GeneratePermitDocument::class,
            [
                'irhpPermit' => $irhpPermitId2,
            ],
            (new Result())->addId('permits', 202)->addMessage('Permit #2 generated')
        );

        $this->expectedSideEffect(
            GeneratePermitDocument::class,
            [
                'irhpPermit' => $irhpPermitId3,
            ],
            (new Result())->addId('permits', 203)->addMessage('Permit #3 generated')
        );

        $result = $this->sut->handleCommand($command);

        $this->assertInstanceOf(Result::class, $result);
        $expected = [
            'id' => [
                'permits' => [201, 202, 203],
                'letters' => [101, 102, 103],
            ],
            'messages' => [
                'Cover letter #1 generated',
                'Permit #1 generated',
                'Cover letter #2 generated',
                'Permit #2 generated',
                'Cover letter #3 generated',
                'Permit #3 generated'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandForBilateral()
    {
        $irhpPermitId1 = 1;
        $irhpPermitId2 = 2;
        $irhpPermitId3 = 3;

        $licenceId1 = 10;
        $licenceId2 = 11;

        $command = Cmd::Create(
            [
                'ids' => [
                    $irhpPermitId1, $irhpPermitId2, $irhpPermitId3
                ]
            ]
        );

        $irhpPermitType = m::mock(IrhpPermitTypeEntity::class);
        $irhpPermitType->shouldReceive('isBilateral')
            ->andReturn(true)
            ->shouldReceive('isMultilateral')
            ->andReturn(false)
            ->shouldReceive('isEcmtShortTerm')
            ->andReturn(false)
            ->shouldReceive('isEcmtRemoval')
            ->andReturn(false);

        $irhpPermitApplication1 = m::mock(IrhpPermitApplicationEntity::class);
        $irhpPermitApplication1->shouldReceive('getIrhpPermitWindow->getIrhpPermitStock->getIrhpPermitType')
            ->andReturn($irhpPermitType);
        $irhpPermitApplication1->shouldReceive('getRelatedApplication->getLicence->getId')
            ->andReturn($licenceId1);

        $irhpPermitApplication2 = m::mock(IrhpPermitApplicationEntity::class);
        $irhpPermitApplication2->shouldReceive('getIrhpPermitWindow->getIrhpPermitStock->getIrhpPermitType')
            ->andReturn($irhpPermitType);
        $irhpPermitApplication2->shouldReceive('getRelatedApplication->getLicence->getId')
            ->andReturn($licenceId1);

        $irhpPermitApplication3 = m::mock(IrhpPermitApplicationEntity::class);
        $irhpPermitApplication3->shouldReceive('getIrhpPermitWindow->getIrhpPermitStock->getIrhpPermitType')
            ->andReturn($irhpPermitType);
        $irhpPermitApplication3->shouldReceive('getRelatedApplication->getLicence->getId')
            ->andReturn($licenceId2);

        $irhpPermit1 = m::mock(IrhpPermitEntity::class);
        $irhpPermit1->shouldReceive('getIrhpPermitApplication')->andReturn($irhpPermitApplication1);
        $irhpPermit1->shouldReceive('getId')->andReturn($irhpPermitId1);

        $irhpPermit2 = m::mock(IrhpPermitEntity::class);
        $irhpPermit2->shouldReceive('getIrhpPermitApplication')->andReturn($irhpPermitApplication2);
        $irhpPermit2->shouldReceive('getId')->andReturn($irhpPermitId2);

        $irhpPermit3 = m::mock(IrhpPermitEntity::class);
        $irhpPermit3->shouldReceive('getIrhpPermitApplication')->andReturn($irhpPermitApplication3);
        $irhpPermit3->shouldReceive('getId')->andReturn($irhpPermitId3);

        $this->repoMap['IrhpPermit']->shouldReceive('fetchById')
            ->with($irhpPermitId1, Query::HYDRATE_OBJECT)
            ->andReturn($irhpPermit1)
            ->shouldReceive('fetchById')
            ->with($irhpPermitId2, Query::HYDRATE_OBJECT)
            ->andReturn($irhpPermit2)
            ->shouldReceive('fetchById')
            ->with($irhpPermitId3, Query::HYDRATE_OBJECT)
            ->andReturn($irhpPermit3);

        $this->expectedSideEffect(
            GenerateCoverLetterDocument::class,
            [
                'irhpPermit' => $irhpPermitId1,
            ],
            (new Result())->addId('letters', 101)->addMessage('Cover letter #1 generated')
        );

        $this->expectedSideEffect(
            GenerateCoverLetterDocument::class,
            [
                'irhpPermit' => $irhpPermitId3,
            ],
            (new Result())->addId('letters', 103)->addMessage('Cover letter #3 generated')
        );

        $this->expectedSideEffect(
            GeneratePermitDocument::class,
            [
                'irhpPermit' => $irhpPermitId1,
            ],
            (new Result())->addId('permits', 201)->addMessage('Permit #1 generated')
        );

        $this->expectedSideEffect(
            GeneratePermitDocument::class,
            [
                'irhpPermit' => $irhpPermitId2,
            ],
            (new Result())->addId('permits', 202)->addMessage('Permit #2 generated')
        );

        $this->expectedSideEffect(
            GeneratePermitDocument::class,
            [
                'irhpPermit' => $irhpPermitId3,
            ],
            (new Result())->addId('permits', 203)->addMessage('Permit #3 generated')
        );

        $result = $this->sut->handleCommand($command);

        $this->assertInstanceOf(Result::class, $result);
        $expected = [
            'id' => [
                'permits' => [201, 202, 203],
                'letters' => [101, 103],
            ],
            'messages' => [
                'Cover letter #1 generated',
                'Permit #1 generated',
                'Permit #2 generated',
                'Cover letter #3 generated',
                'Permit #3 generated'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandForMultilateral()
    {
        $irhpPermitId1 = 1;
        $irhpPermitId2 = 2;
        $irhpPermitId3 = 3;

        $licenceId1 = 10;
        $licenceId2 = 11;

        $command = Cmd::Create(
            [
                'ids' => [
                    $irhpPermitId1, $irhpPermitId2, $irhpPermitId3
                ]
            ]
        );

        $irhpPermitType = m::mock(IrhpPermitTypeEntity::class);
        $irhpPermitType->shouldReceive('isBilateral')
            ->andReturn(false)
            ->shouldReceive('isMultilateral')
            ->andReturn(true)
            ->shouldReceive('isEcmtShortTerm')
            ->andReturn(false)
            ->shouldReceive('isEcmtRemoval')
            ->andReturn(false);

        $irhpPermitApplication1 = m::mock(IrhpPermitApplicationEntity::class);
        $irhpPermitApplication1->shouldReceive('getIrhpPermitWindow->getIrhpPermitStock->getIrhpPermitType')
            ->andReturn($irhpPermitType);
        $irhpPermitApplication1->shouldReceive('getRelatedApplication->getLicence->getId')
            ->andReturn($licenceId1);

        $irhpPermitApplication2 = m::mock(IrhpPermitApplicationEntity::class);
        $irhpPermitApplication2->shouldReceive('getIrhpPermitWindow->getIrhpPermitStock->getIrhpPermitType')
            ->andReturn($irhpPermitType);
        $irhpPermitApplication2->shouldReceive('getRelatedApplication->getLicence->getId')
            ->andReturn($licenceId1);

        $irhpPermitApplication3 = m::mock(IrhpPermitApplicationEntity::class);
        $irhpPermitApplication3->shouldReceive('getIrhpPermitWindow->getIrhpPermitStock->getIrhpPermitType')
            ->andReturn($irhpPermitType);
        $irhpPermitApplication3->shouldReceive('getRelatedApplication->getLicence->getId')
            ->andReturn($licenceId2);

        $irhpPermit1 = m::mock(IrhpPermitEntity::class);
        $irhpPermit1->shouldReceive('getIrhpPermitApplication')->andReturn($irhpPermitApplication1);
        $irhpPermit1->shouldReceive('getId')->andReturn($irhpPermitId1);

        $irhpPermit2 = m::mock(IrhpPermitEntity::class);
        $irhpPermit2->shouldReceive('getIrhpPermitApplication')->andReturn($irhpPermitApplication2);
        $irhpPermit2->shouldReceive('getId')->andReturn($irhpPermitId2);

        $irhpPermit3 = m::mock(IrhpPermitEntity::class);
        $irhpPermit3->shouldReceive('getIrhpPermitApplication')->andReturn($irhpPermitApplication3);
        $irhpPermit3->shouldReceive('getId')->andReturn($irhpPermitId3);

        $this->repoMap['IrhpPermit']->shouldReceive('fetchById')
            ->with($irhpPermitId1, Query::HYDRATE_OBJECT)
            ->andReturn($irhpPermit1)
            ->shouldReceive('fetchById')
            ->with($irhpPermitId2, Query::HYDRATE_OBJECT)
            ->andReturn($irhpPermit2)
            ->shouldReceive('fetchById')
            ->with($irhpPermitId3, Query::HYDRATE_OBJECT)
            ->andReturn($irhpPermit3);

        $this->expectedSideEffect(
            GenerateCoverLetterDocument::class,
            [
                'irhpPermit' => $irhpPermitId1,
            ],
            (new Result())->addId('letters', 101)->addMessage('Cover letter #1 generated')
        );

        $this->expectedSideEffect(
            GenerateCoverLetterDocument::class,
            [
                'irhpPermit' => $irhpPermitId3,
            ],
            (new Result())->addId('letters', 103)->addMessage('Cover letter #3 generated')
        );

        $this->expectedSideEffect(
            GeneratePermitDocument::class,
            [
                'irhpPermit' => $irhpPermitId1,
            ],
            (new Result())->addId('permits', 201)->addMessage('Permit #1 generated')
        );

        $this->expectedSideEffect(
            GeneratePermitDocument::class,
            [
                'irhpPermit' => $irhpPermitId2,
            ],
            (new Result())->addId('permits', 202)->addMessage('Permit #2 generated')
        );

        $this->expectedSideEffect(
            GeneratePermitDocument::class,
            [
                'irhpPermit' => $irhpPermitId3,
            ],
            (new Result())->addId('permits', 203)->addMessage('Permit #3 generated')
        );

        $result = $this->sut->handleCommand($command);

        $this->assertInstanceOf(Result::class, $result);
        $expected = [
            'id' => [
                'permits' => [201, 202, 203],
                'letters' => [101, 103],
            ],
            'messages' => [
                'Cover letter #1 generated',
                'Permit #1 generated',
                'Permit #2 generated',
                'Cover letter #3 generated',
                'Permit #3 generated'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandForEcmtShortTerm()
    {
        $irhpPermitId1 = 1;
        $irhpPermitId2 = 2;
        $irhpPermitId3 = 3;

        $licenceId1 = 10;
        $licenceId2 = 11;

        $command = Cmd::Create(
            [
                'ids' => [
                    $irhpPermitId1, $irhpPermitId2, $irhpPermitId3
                ]
            ]
        );

        $irhpPermitType = m::mock(IrhpPermitTypeEntity::class);
        $irhpPermitType->shouldReceive('isBilateral')
            ->andReturn(false)
            ->shouldReceive('isMultilateral')
            ->andReturn(false)
            ->shouldReceive('isEcmtShortTerm')
            ->andReturn(true)
            ->shouldReceive('isEcmtRemoval')
            ->andReturn(false);

        $irhpPermitApplication1 = m::mock(IrhpPermitApplicationEntity::class);
        $irhpPermitApplication1->shouldReceive('getIrhpPermitWindow->getIrhpPermitStock->getIrhpPermitType')
            ->andReturn($irhpPermitType);
        $irhpPermitApplication1->shouldReceive('getRelatedApplication->getLicence->getId')
            ->andReturn($licenceId1);

        $irhpPermitApplication2 = m::mock(IrhpPermitApplicationEntity::class);
        $irhpPermitApplication2->shouldReceive('getIrhpPermitWindow->getIrhpPermitStock->getIrhpPermitType')
            ->andReturn($irhpPermitType);
        $irhpPermitApplication2->shouldReceive('getRelatedApplication->getLicence->getId')
            ->andReturn($licenceId1);

        $irhpPermitApplication3 = m::mock(IrhpPermitApplicationEntity::class);
        $irhpPermitApplication3->shouldReceive('getIrhpPermitWindow->getIrhpPermitStock->getIrhpPermitType')
            ->andReturn($irhpPermitType);
        $irhpPermitApplication3->shouldReceive('getRelatedApplication->getLicence->getId')
            ->andReturn($licenceId2);

        $irhpPermit1 = m::mock(IrhpPermitEntity::class);
        $irhpPermit1->shouldReceive('getIrhpPermitApplication')->andReturn($irhpPermitApplication1);
        $irhpPermit1->shouldReceive('getId')->andReturn($irhpPermitId1);

        $irhpPermit2 = m::mock(IrhpPermitEntity::class);
        $irhpPermit2->shouldReceive('getIrhpPermitApplication')->andReturn($irhpPermitApplication2);
        $irhpPermit2->shouldReceive('getId')->andReturn($irhpPermitId2);

        $irhpPermit3 = m::mock(IrhpPermitEntity::class);
        $irhpPermit3->shouldReceive('getIrhpPermitApplication')->andReturn($irhpPermitApplication3);
        $irhpPermit3->shouldReceive('getId')->andReturn($irhpPermitId3);

        $this->repoMap['IrhpPermit']->shouldReceive('fetchById')
            ->with($irhpPermitId1, Query::HYDRATE_OBJECT)
            ->andReturn($irhpPermit1)
            ->shouldReceive('fetchById')
            ->with($irhpPermitId2, Query::HYDRATE_OBJECT)
            ->andReturn($irhpPermit2)
            ->shouldReceive('fetchById')
            ->with($irhpPermitId3, Query::HYDRATE_OBJECT)
            ->andReturn($irhpPermit3);

        $this->expectedSideEffect(
            GenerateCoverLetterDocument::class,
            [
                'irhpPermit' => $irhpPermitId1,
            ],
            (new Result())->addId('letters', 101)->addMessage('Cover letter #1 generated')
        );

        $this->expectedSideEffect(
            GenerateCoverLetterDocument::class,
            [
                'irhpPermit' => $irhpPermitId3,
            ],
            (new Result())->addId('letters', 103)->addMessage('Cover letter #3 generated')
        );

        $this->expectedSideEffect(
            GeneratePermitDocument::class,
            [
                'irhpPermit' => $irhpPermitId1,
            ],
            (new Result())->addId('permits', 201)->addMessage('Permit #1 generated')
        );

        $this->expectedSideEffect(
            GeneratePermitDocument::class,
            [
                'irhpPermit' => $irhpPermitId2,
            ],
            (new Result())->addId('permits', 202)->addMessage('Permit #2 generated')
        );

        $this->expectedSideEffect(
            GeneratePermitDocument::class,
            [
                'irhpPermit' => $irhpPermitId3,
            ],
            (new Result())->addId('permits', 203)->addMessage('Permit #3 generated')
        );

        $result = $this->sut->handleCommand($command);

        $this->assertInstanceOf(Result::class, $result);
        $expected = [
            'id' => [
                'permits' => [201, 202, 203],
                'letters' => [101, 103],
            ],
            'messages' => [
                'Cover letter #1 generated',
                'Permit #1 generated',
                'Permit #2 generated',
                'Cover letter #3 generated',
                'Permit #3 generated'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandForEcmtRemoval()
    {
        $irhpPermitId1 = 1;
        $irhpPermitId2 = 2;
        $irhpPermitId3 = 3;

        $licenceId1 = 10;
        $licenceId2 = 11;

        $command = Cmd::Create(
            [
                'ids' => [
                    $irhpPermitId1, $irhpPermitId2, $irhpPermitId3
                ]
            ]
        );

        $irhpPermitType = m::mock(IrhpPermitTypeEntity::class);
        $irhpPermitType->shouldReceive('isBilateral')
            ->andReturn(false)
            ->shouldReceive('isMultilateral')
            ->andReturn(false)
            ->shouldReceive('isEcmtShortTerm')
            ->andReturn(false)
            ->shouldReceive('isEcmtRemoval')
            ->andReturn(true);

        $irhpPermitApplication1 = m::mock(IrhpPermitApplicationEntity::class);
        $irhpPermitApplication1->shouldReceive('getIrhpPermitWindow->getIrhpPermitStock->getIrhpPermitType')
            ->andReturn($irhpPermitType);
        $irhpPermitApplication1->shouldReceive('getRelatedApplication->getLicence->getId')
            ->andReturn($licenceId1);

        $irhpPermitApplication2 = m::mock(IrhpPermitApplicationEntity::class);
        $irhpPermitApplication2->shouldReceive('getIrhpPermitWindow->getIrhpPermitStock->getIrhpPermitType')
            ->andReturn($irhpPermitType);
        $irhpPermitApplication2->shouldReceive('getRelatedApplication->getLicence->getId')
            ->andReturn($licenceId1);

        $irhpPermitApplication3 = m::mock(IrhpPermitApplicationEntity::class);
        $irhpPermitApplication3->shouldReceive('getIrhpPermitWindow->getIrhpPermitStock->getIrhpPermitType')
            ->andReturn($irhpPermitType);
        $irhpPermitApplication3->shouldReceive('getRelatedApplication->getLicence->getId')
            ->andReturn($licenceId2);

        $irhpPermit1 = m::mock(IrhpPermitEntity::class);
        $irhpPermit1->shouldReceive('getIrhpPermitApplication')->andReturn($irhpPermitApplication1);
        $irhpPermit1->shouldReceive('getId')->andReturn($irhpPermitId1);

        $irhpPermit2 = m::mock(IrhpPermitEntity::class);
        $irhpPermit2->shouldReceive('getIrhpPermitApplication')->andReturn($irhpPermitApplication2);
        $irhpPermit2->shouldReceive('getId')->andReturn($irhpPermitId2);

        $irhpPermit3 = m::mock(IrhpPermitEntity::class);
        $irhpPermit3->shouldReceive('getIrhpPermitApplication')->andReturn($irhpPermitApplication3);
        $irhpPermit3->shouldReceive('getId')->andReturn($irhpPermitId3);

        $this->repoMap['IrhpPermit']->shouldReceive('fetchById')
            ->with($irhpPermitId1, Query::HYDRATE_OBJECT)
            ->andReturn($irhpPermit1)
            ->shouldReceive('fetchById')
            ->with($irhpPermitId2, Query::HYDRATE_OBJECT)
            ->andReturn($irhpPermit2)
            ->shouldReceive('fetchById')
            ->with($irhpPermitId3, Query::HYDRATE_OBJECT)
            ->andReturn($irhpPermit3);

        $this->expectedSideEffect(
            GenerateCoverLetterDocument::class,
            [
                'irhpPermit' => $irhpPermitId1,
            ],
            (new Result())->addId('letters', 101)->addMessage('Cover letter #1 generated')
        );

        $this->expectedSideEffect(
            GenerateCoverLetterDocument::class,
            [
                'irhpPermit' => $irhpPermitId3,
            ],
            (new Result())->addId('letters', 103)->addMessage('Cover letter #3 generated')
        );

        $this->expectedSideEffect(
            GeneratePermitDocument::class,
            [
                'irhpPermit' => $irhpPermitId1,
            ],
            (new Result())->addId('permits', 201)->addMessage('Permit #1 generated')
        );

        $this->expectedSideEffect(
            GeneratePermitDocument::class,
            [
                'irhpPermit' => $irhpPermitId2,
            ],
            (new Result())->addId('permits', 202)->addMessage('Permit #2 generated')
        );

        $this->expectedSideEffect(
            GeneratePermitDocument::class,
            [
                'irhpPermit' => $irhpPermitId3,
            ],
            (new Result())->addId('permits', 203)->addMessage('Permit #3 generated')
        );

        $result = $this->sut->handleCommand($command);

        $this->assertInstanceOf(Result::class, $result);
        $expected = [
            'id' => [
                'permits' => [201, 202, 203],
                'letters' => [101, 103],
            ],
            'messages' => [
                'Cover letter #1 generated',
                'Permit #1 generated',
                'Permit #2 generated',
                'Cover letter #3 generated',
                'Permit #3 generated'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
