<?php

/**
 * Generate Irfo Psv Auth Test
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Irfo;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Document\GenerateAndStore;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Irfo\GenerateIrfoPsvAuth as Sut;
use Dvsa\Olcs\Api\Domain\Repository\IrfoPsvAuth as IrfoPsvAuthRepo;
use Dvsa\Olcs\Api\Domain\Repository\Fee as FeeRepo;
use Dvsa\Olcs\Api\Entity\Irfo\IrfoPsvAuth as IrfoPsvAuthEntity;
use Dvsa\Olcs\Api\Entity\Irfo\IrfoPsvAuthType as IrfoPsvAuthTypeEntity;
use Dvsa\Olcs\Api\Entity\System\Category as CategoryEntity;
use Dvsa\Olcs\Api\Entity\System\SubCategory as SubCategoryEntity;
use Dvsa\Olcs\Transfer\Command\Irfo\GenerateIrfoPsvAuth as Cmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

/**
 * Generate Irfo Psv Auth Test
 */
class GenerateIrfoPsvAuthTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new Sut();
        $this->mockRepo('IrfoPsvAuth', IrfoPsvAuthRepo::class);
        $this->mockRepo('Fee', FeeRepo::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            IrfoPsvAuthEntity::JOURNEY_FREQ_DAILY,
        ];

        $this->references = [
            IrfoPsvAuthTypeEntity::class => [
                22 => m::mock(IrfoPsvAuthTypeEntity::class)
            ],
        ];

        parent::initReferences();
    }

    /**
     * @dataProvider handleCommandProvider
     *
     * @param int $irfoFeeTypeId
     * @param array $expectedDocs
     */
    public function testHandleCommand($irfoFeeTypeId, $expectedDocs)
    {
        $id = 99;
        $orgId = 101;

        $data = [
            'id' => $id,
            'version' => 2,
            'irfoPsvAuthType' => 22,
            'journeyFrequency' => IrfoPsvAuthEntity::JOURNEY_FREQ_DAILY,
            'irfoPsvAuthNumbers' => [],
            'copiesRequiredTotal' => 5,
        ];

        $command = Cmd::create($data);

        $this->repoMap['Fee']->shouldReceive('fetchFeesByIrfoPsvAuthId')
            ->with($id, true)
            ->andReturn(['FEE']);

        /** @var IrfoPsvAuthEntity $irfoPsvAuth */
        $irfoPsvAuth = m::mock(IrfoPsvAuthEntity::class)->makePartial();
        $irfoPsvAuth->setIrfoPsvAuthNumbers([]);
        $irfoPsvAuth->shouldReceive('update')->once();
        $irfoPsvAuth->shouldReceive('generate')->once()->with(['FEE'])->shouldReceive('getId')->andReturn($id);
        $irfoPsvAuth->shouldReceive('getIrfoPsvAuthType->getIrfoFeeType->getId')->once()->andReturn($irfoFeeTypeId);
        $irfoPsvAuth->shouldReceive('getOrganisation->getId')->andReturn($orgId);

        $this->repoMap['IrfoPsvAuth']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT, 2)
            ->andReturn($irfoPsvAuth)
            ->shouldReceive('save')
            ->with(m::type(IrfoPsvAuthEntity::class))
            ->once();

        foreach ($expectedDocs as $expectedTemplate) {
            $this->expectedSideEffect(
                GenerateAndStore::class,
                [
                    'template' => $expectedTemplate,
                    'query' => [
                        'irfoPsvAuth' => $id,
                        'organisation' => $orgId
                    ],
                    'knownValues' => [],
                    'description' => 'IRFO PSV Authorisation (99) x 5',
                    'irfoOrganisation' => $orgId,
                    'category' => CategoryEntity::CATEGORY_IRFO,
                    'subCategory' => SubCategoryEntity::DOC_SUB_CATEGORY_IRFO_CONTINUATIONS_AND_RENEWALS,
                    'isExternal' => false,
                    'isScan' => false
                ],
                new Result()
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
                ['IRFO_eu_auth_pink_GV280']
            ],
            [
                IrfoPsvAuthTypeEntity::IRFO_FEE_TYPE_EU_REG_19A,
                ['IRFO_eu_auth_pink_GV280']
            ],
            [
                IrfoPsvAuthTypeEntity::IRFO_FEE_TYPE_NON_EU_OCCASIONAL_19,
                ['IRFO_eu_auth_pink_special_regular_GV280']
            ],
            [
                IrfoPsvAuthTypeEntity::IRFO_FEE_TYPE_NON_EU_REG_18,
                [
                    'IRFO_uk_green_authorisation_INT_P17',
                    'IRFO_non_eu_blue_authorisation_to_foreign_partner_INT_P18',
                ]
            ],
            [
                IrfoPsvAuthTypeEntity::IRFO_FEE_TYPE_NON_EU_REG_19,
                ['IRFO_non_eu_blue_authorisation_foreign_operator_no_partner_INT_P18A']
            ],
            [
                IrfoPsvAuthTypeEntity::IRFO_FEE_TYPE_SHUTTLE_OPERATOR_20,
                ['IRFO_eu_auth_pink_special_regular_GV280']
            ],
            [
                IrfoPsvAuthTypeEntity::IRFO_FEE_TYPE_OWN_AC_21,
                ['IRFO_own_acc']
            ],
        ];
    }
}
