<?php

namespace Dvsa\OlcsTest\Api\Domain\Validation\Handlers\Messaging;

use Dvsa\Olcs\Api\Domain\Validation\Handlers\Messaging\CanCreateConversationForOrganisation;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\OlcsTest\Api\Domain\Validation\Handlers\AbstractHandlerTestCase;
use Mockery as m;

class CanCreateConversationForOrganisationTest extends AbstractHandlerTestCase
{
    /**
     * @var CanCreateConversationForOrganisation
     */
    protected $sut;

    public function setUp(): void
    {
        $this->sut = new CanCreateConversationForOrganisation();

        parent::setUp();
    }

    /**
     * @dataProvider dpTestIsValid
     */
    public function testIsValid($canAccess, $hasPermission, $expected)
    {
        /** @var CommandInterface $dto */
        $orgId = 1;
        $permission = Permission::CAN_CREATE_CONVERSATION;
        $dto = m::mock(CommandInterface::class);

        $this->setIsGranted($permission, $hasPermission);

        if ($hasPermission === false) {
            $this->assertSame($expected, $this->sut->isValid($dto));
        } else {
            $this->setIsValid('canAccessLicence', [$orgId], $canAccess);
            $dto->shouldReceive('getLicence')->twice()->andReturn($orgId);

            $this->assertSame($expected, $this->sut->isValid($dto));
        }
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
