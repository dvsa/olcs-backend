<?php

/**
 * Renew IrfoPsvAuth Test
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Irfo;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\Irfo\RenewIrfoPsvAuth as Sut;
use Dvsa\Olcs\Api\Domain\Repository\IrfoPsvAuth;
use Dvsa\Olcs\Api\Entity\Irfo\IrfoPsvAuth as IrfoPsvAuthEntity;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Transfer\Command\Irfo\RenewIrfoPsvAuth as Cmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;

/**
 * Renew IrfoPsvAuth Test
 */
class RenewIrfoPsvAuthTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new Sut();
        $this->mockRepo('IrfoPsvAuth', IrfoPsvAuth::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            IrfoPsvAuthEntity::STATUS_RENEW
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $data = [
            'ids' => array_fill(0, Sut::MAX_IDS_COUNT, 'id')
        ];

        $command = Cmd::create($data);

        /** @var IrfoPsvAuthEntity $irfoPsvAuth */
        $irfoPsvAuth1 = m::mock(IrfoPsvAuthEntity::class)->makePartial();
        $irfoPsvAuth1->setId(1001);
        $irfoPsvAuth1->setStatus(new RefData(IrfoPsvAuthEntity::STATUS_PENDING));

        $irfoPsvAuth2 = m::mock(IrfoPsvAuthEntity::class)->makePartial();
        $irfoPsvAuth2->setId(1002);
        $irfoPsvAuth2->setStatus(new RefData(IrfoPsvAuthEntity::STATUS_APPROVED));

        $this->repoMap['IrfoPsvAuth']->shouldReceive('fetchByIds')
            ->once()
            ->with($data['ids'])
            ->andReturn([$irfoPsvAuth1, $irfoPsvAuth2]);

        $savedIrfoPsvAuths = [];

        $this->repoMap['IrfoPsvAuth']->shouldReceive('save')
            ->times(2)
            ->with(m::type(IrfoPsvAuthEntity::class))
            ->andReturnUsing(
                function (IrfoPsvAuthEntity $irfoPsvAuth) use (&$savedIrfoPsvAuths) {
                    $savedIrfoPsvAuths[] = $irfoPsvAuth;
                }
            );

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'IRFO PSV Auth renewed successfully'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());

        $this->assertEquals(2, sizeof($savedIrfoPsvAuths));

        foreach ($savedIrfoPsvAuths as $savedIrfoPsvAuth) {
            $this->assertSame(
                $this->refData[IrfoPsvAuthEntity::STATUS_RENEW],
                $savedIrfoPsvAuth->getStatus()
            );
        }
    }

    public function testHandleCommandWithMaxIdsCountExceeded()
    {
        $this->expectException(\Dvsa\Olcs\Api\Domain\Exception\ValidationException::class);

        $data = [
            'ids' => array_fill(0, Sut::MAX_IDS_COUNT + 1, 'id')
        ];

        $command = Cmd::create($data);

        $this->sut->handleCommand($command);
    }
}
