<?php

namespace Dvsa\OlcsTest\Api\Domain\Validation\Handlers\Misc;

use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\CanAccessTransactionWithRef;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\OlcsTest\Api\Domain\Validation\Handlers\AbstractHandlerTestCase;
use Mockery as m;

/**
 * @covers Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\CanAccessTransactionWithRef
 */
class CanAccessTransactionWithRefTest extends AbstractHandlerTestCase
{
    /**
     * @var CanAccessTransactionWithId
     */
    protected $sut;

    public function setUp(): void
    {
        $this->sut = new CanAccessTransactionWithRef();

        parent::setUp();
    }

    /**
     * @dataProvider dataProvider
     *
     * @param $expected
     * @param $isValid
     */
    public function testIsValid($expected, $isValid)
    {
        /** @var CommandInterface $dto */
        $dto = m::mock(CommandInterface::class);
        $dto->shouldReceive('getReference')->andReturn('OLCS-12345');

        $this->setIsValid('canAccessTransaction', ['OLCS-12345'], $isValid);

        $this->assertSame($expected, $this->sut->isValid($dto));
    }

    public function dataProvider()
    {
        return [
            [true, true],
            [false, false],
        ];
    }
}
