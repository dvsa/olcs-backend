<?php

namespace Dvsa\OlcsTest\Api\Domain\Validation\Handlers\Messaging;

use Dvsa\Olcs\Api\Domain\Validation\Handlers\Messaging\CanCreateMessageWithConversation;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\OlcsTest\Api\Domain\Repository\RepositoryTestCase;
use Dvsa\OlcsTest\Api\Domain\Validation\Handlers\AbstractHandlerTestCase;
use Mockery as m;

class CanCreateMessageWithConversationTest extends AbstractHandlerTestCase
{
    /**
     * @var CanCreateMessageWithConversation
     */
    protected $sut;

    public function setUp(): void
    {
        $this->sut = new CanCreateMessageWithConversation;

        parent::setUp();
    }

    /**
     * @dataProvider dpTestIsValid
     */
    public function testIsValid($canAccess, $hasPermission, $expected)
    {
        /** @var CommandInterface $dto */
        $conversationId = 1;
        $permission = Permission::CAN_REPLY_TO_CONVERSATIONS;
        $dto = m::mock(CommandInterface::class);

        $this->setIsGranted($permission, $hasPermission);

        if ($hasPermission) {
            $dto->shouldReceive('getConversation')->once()->andReturn($conversationId);
            m::mock(Dvsa\Olcs\Api\Domain\Repository\MessagingConversation);
        }

        $this->setIsValid('canAccessOrganisation', [$conversationId], $canAccess);

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
