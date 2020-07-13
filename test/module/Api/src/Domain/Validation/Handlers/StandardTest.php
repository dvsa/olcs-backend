<?php

/**
 * Standard Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Validation\Handlers;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Standard;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use OlcsTest\Bootstrap;

/**
 * Standard Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class StandardTest extends MockeryTestCase
{
    /**
     * @var Standard
     */
    private $sut;

    public function setUp(): void
    {
        $this->sut = new Standard();

        Bootstrap::setupLogger();
    }

    public function testIsValid()
    {
        $dto = m::mock();

        $this->assertTrue($this->sut->isValid($dto));
    }
}
