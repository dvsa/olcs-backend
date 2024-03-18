<?php

namespace Dvsa\OlcsTest\Api\Domain\Validation\Handlers\Messaging;

use Dvsa\Olcs\Api\Domain\Validation\Handlers\Messaging\CanCloseConversationWithId;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\OlcsTest\Api\Domain\Validation\Handlers\AbstractHandlerTestCase;
use Mockery as m;

class CanCloseConversationWithIdTest extends AbstractHandlerTestCase
{
    /**
     * @var CanCloseConversationWithId
     */
    protected $sut;

    public function setUp(): void
    {
        $this->sut = new CanCloseConversationWithId();

        parent::setUp();
    }

    /**
     * @dataProvider dpTestIsValid
     */
    public function testIsValid($canAccess, $hasPermission, $expected)
    {
        /** @var CommandInterface $dto */
        $conversationId = 1;
        $permission = Permission::CAN_CLOSE_CONVERSATION;
        $dto = m::mock(CommandInterface::class);

        if ($hasPermission) {
            $dto->shouldReceive('getId')->once()->andReturn($conversationId);
        }

        $this->setIsGranted($permission, $hasPermission);

        $this->setIsValid('canAccessConversation', [$conversationId], $canAccess);

        $this->assertSame($expected, $this->sut->isValid($dto));
    }

    public function dpTestIsValid()
    {
        return [
            [true, true, true],
            [true, false, false],
            [false, true, false],
            [false, false, false],
        ];
    }
}
