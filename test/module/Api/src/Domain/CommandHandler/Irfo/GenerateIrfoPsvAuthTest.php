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
use Dvsa\Olcs\Api\Entity\System\Category as CategoryEntity;
use Dvsa\Olcs\Api\Entity\System\SubCategory as SubCategoryEntity;
use Dvsa\Olcs\Transfer\Command\Irfo\GenerateIrfoPsvAuth as Cmd;
use Dvsa\Olcs\Transfer\Command\Irfo\UpdateIrfoPsvAuth as UpdateIrfoPsvAuthCmd;
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

    /**
     * testHandleCommand
     */
    public function testHandleCommand()
    {
        $id = 99;
        $orgId = 101;

        $data = [
            'id' => $id,
            'version' => 2,
            'copiesRequiredTotal' => 5,
        ];

        $command = Cmd::create($data);

        // handle update
        $this->expectedSideEffect(
            UpdateIrfoPsvAuthCmd::class, $command->getArrayCopy(),
            (new Result())->addMessage('IRFO PSV Auth generated successfully')
                ->addId('irfoPsvAuth', $data['id'])
        );

        $this->repoMap['Fee']->shouldReceive('fetchFeesByIrfoPsvAuthId')
            ->with($id, true)
            ->andReturn(['FEE']);

        /** @var IrfoPsvAuthEntity $irfoPsvAuth */
        $irfoPsvAuth = m::mock(IrfoPsvAuthEntity::class);
        $irfoPsvAuth->shouldReceive('generate')->once()->with(['FEE'])->shouldReceive('getId')->andReturn($id);
        $irfoPsvAuth->shouldReceive('getIrfoPsvAuthType->getSectionCode')->andReturn('section code');
        $irfoPsvAuth->shouldReceive('getOrganisation->getId')->andReturn($orgId);

        $this->repoMap['IrfoPsvAuth']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT)
            ->andReturn($irfoPsvAuth)
            ->shouldReceive('save')
            ->with(m::type(IrfoPsvAuthEntity::class))
            ->once();

        $this->expectedSideEffect(
            GenerateAndStore::class,
            [
                'template' => 'IRFO_PSV_section_code',
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

        $result = $this->sut->handleCommand($command);

        $this->assertInstanceOf(Result::class, $result);
    }
}
