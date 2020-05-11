<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\User;

use Dvsa\Olcs\Api\Domain\Repository\User;
use Dvsa\Olcs\Api\Domain\CommandHandler\User\UpdateUserLastLoginAt as Sut;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;

class UpdateUserLastLoginAtTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new Sut();
        $this->mockRepo('User', User::class);
    }

    public function testUserLastLoginAtIsUpdatedToCurrentTimestamp()
    {

    }
}
