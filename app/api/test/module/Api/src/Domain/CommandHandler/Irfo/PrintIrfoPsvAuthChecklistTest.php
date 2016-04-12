<?php

/**
 * Print IRFO PSV Auth Checklist Test
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Irfo;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\Command\Document\GenerateAndStore;
use Dvsa\Olcs\Api\Domain\Command\PrintScheduler\Enqueue as EnqueueFileCommand;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Irfo\PrintIrfoPsvAuthChecklist as Sut;
use Dvsa\Olcs\Api\Domain\Repository\IrfoPsvAuth;
use Dvsa\Olcs\Api\Entity\Irfo\IrfoPsvAuth as IrfoPsvAuthEntity;
use Dvsa\Olcs\Api\Entity\Irfo\IrfoPsvAuthType as IrfoPsvAuthTypeEntity;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation as OrganisationEntity;
use Dvsa\Olcs\Api\Entity\System\Category as CategoryEntity;
use Dvsa\Olcs\Api\Entity\System\SubCategory as SubCategoryEntity;
use Dvsa\Olcs\Transfer\Command\Irfo\PrintIrfoPsvAuthChecklist as Cmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;

/**
 * Print IRFO PSV Auth Checklist Test
 */
class PrintIrfoPsvAuthChecklistTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new Sut();
        $this->mockRepo('IrfoPsvAuth', IrfoPsvAuth::class);

        parent::setUp();
    }

    /**
     * @dataProvider handleCommandProvider
     *
     * @param int $irfoFeeTypeId
     * @param array $expectedDocs
     */
    public function testHandleCommand($irfoFeeTypeId, $expectedDocs)
    {
        $data = [
            'ids' => array_fill(0, Sut::MAX_IDS_COUNT, 'id')
        ];

        $command = Cmd::create($data);

        $orgId = 101;
        $org = m::mock(OrganisationEntity::class)->makePartial();
        $org->setId($orgId);

        /** @var IrfoPsvAuthEntity $irfoPsvAuth */
        $irfoPsvAuth1 = m::mock(IrfoPsvAuthEntity::class)->makePartial();
        $irfoPsvAuth1->setId(1001);
        $irfoPsvAuth1->setOrganisation($org);
        $irfoPsvAuth1
            ->shouldReceive('getIrfoPsvAuthType->getIrfoFeeType->getId')
            ->once()
            ->andReturn($irfoFeeTypeId);

        $this->repoMap['IrfoPsvAuth']->shouldReceive('fetchByIds')
            ->once()
            ->with($data['ids'])
            ->andReturn([$irfoPsvAuth1]);

        $docId1 = 10333;
        $generateResult1 = new Result();
        $generateResult1->addId('document', $docId1);

        $this->expectedSideEffect(
            GenerateAndStore::class,
            [
                'template' => 'IRFO_Checklist_Renewal_letter',
                'query' => [
                    'irfoPsvAuth' => 1001
                ],
                'knownValues' => [],
                'description' => 'IRFO PSV Auth Checklist Renewal letter (1001)',
                'irfoOrganisation' => $orgId,
                'category' => CategoryEntity::CATEGORY_IRFO,
                'subCategory' => SubCategoryEntity::DOC_SUB_CATEGORY_IRFO_CONTINUATIONS_AND_RENEWALS,
                'isExternal' => false,
                'isScan' => false
            ],
            $generateResult1
        );

        $docQueueResult1 = new Result();
        $docQueueResult1->addMessage('Document queued: ' . $docId1);
        $this->expectedSideEffect(
            EnqueueFileCommand::class,
            [
                'documentId' => $docId1,
                'jobName' => 'IRFO PSV Auth Checklist Renewal letter (1001)'
            ],
            $docQueueResult1
        );

        $docId2 = 20333;
        $generateResult2 = new Result();
        $generateResult2->addId('document', $docId2);

        foreach ($expectedDocs as $expectedTemplate => $expectedDesc) {
            $this->expectedSideEffect(
                GenerateAndStore::class,
                [
                    'template' => $expectedTemplate,
                    'query' => [
                        'irfoPsvAuth' => 1001
                    ],
                    'knownValues' => [],
                    'description' => $expectedDesc,
                    'irfoOrganisation' => $orgId,
                    'category' => CategoryEntity::CATEGORY_IRFO,
                    'subCategory' => SubCategoryEntity::DOC_SUB_CATEGORY_IRFO_CONTINUATIONS_AND_RENEWALS,
                    'isExternal' => false,
                    'isScan' => false
                ],
                $generateResult2
            );

            $docQueueResult2 = new Result();
            $docQueueResult2->addMessage('Document queued: ' . $docId2);
            $this->expectedSideEffect(
                EnqueueFileCommand::class,
                [
                    'documentId' => $docId2,
                    'jobName' => $expectedDesc
                ],
                $docQueueResult2
            );
        }

        $result = $this->sut->handleCommand($command);

        $this->assertInstanceOf(Result::class, $result);
    }

    /**
     * @return array
     */
    public function handleCommandProvider()
    {
        return [
            [
                IrfoPsvAuthTypeEntity::IRFO_FEE_TYPE_EU_REG_17,
                ['IRFO_eu_auth_pink_GV280' => 'IRFO eu auth pink GV280 (1001)']
            ],
            [
                IrfoPsvAuthTypeEntity::IRFO_FEE_TYPE_EU_REG_19A,
                ['IRFO_eu_auth_pink_GV280' => 'IRFO eu auth pink GV280 (1001)']
            ],
            [
                IrfoPsvAuthTypeEntity::IRFO_FEE_TYPE_NON_EU_OCCASIONAL_19,
                ['IRFO_eu_auth_pink_special_regular_GV280' => 'IRFO eu auth pink special regular GV280 (1001)']
            ],
            [
                IrfoPsvAuthTypeEntity::IRFO_FEE_TYPE_NON_EU_REG_18,
                [
                    'IRFO_uk_green_authorisation_INT_P17' => 'IRFO uk green authorisation INT P17 (1001)',
                    'IRFO_non_eu_blue_authorisation_to_foreign_partner_INT_P18'
                        => 'IRFO non eu blue authorisation to foreign partner INT P18 (1001)',
                ]
            ],
            [
                IrfoPsvAuthTypeEntity::IRFO_FEE_TYPE_NON_EU_REG_19,
                [
                    'IRFO_non_eu_blue_authorisation_foreign_operator_no_partner_INT_P18A'
                        => 'IRFO non eu blue authorisation foreign operator no partner INT P18A (1001)'
                ]
            ],
            [
                IrfoPsvAuthTypeEntity::IRFO_FEE_TYPE_SHUTTLE_OPERATOR_20,
                ['IRFO_eu_auth_pink_special_regular_GV280' => 'IRFO eu auth pink special regular GV280 (1001)']
            ],
            [
                IrfoPsvAuthTypeEntity::IRFO_FEE_TYPE_OWN_AC_21,
                ['IRFO_own_acc' => 'IRFO own acc (1001)']
            ],
        ];
    }

    /**
     * @expectedException Dvsa\Olcs\Api\Domain\Exception\ValidationException
     */
    public function testHandleCommandWithMaxIdsCountExceeded()
    {
        $data = [
            'ids' => array_fill(0, Sut::MAX_IDS_COUNT + 1, 'id')
        ];

        $command = Cmd::create($data);

        $this->sut->handleCommand($command);
    }
}
