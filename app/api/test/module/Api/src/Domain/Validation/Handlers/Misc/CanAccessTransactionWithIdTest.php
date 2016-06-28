<?php

namespace Dvsa\OlcsTest\Api\Domain\Validation\Handlers\Fee;

use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\OlcsTest\Api\Domain\Validation\Handlers\AbstractHandlerTestCase;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\CanAccessTransactionWithId;

/**
 * @covers CanAccessTransactionWithId
 */
class CanAccessTransactionWithIdTest extends AbstractHandlerTestCase
{
    /**
     * @var CanAccessTransactionWithId
     */
    protected $sut;

    public function setUp()
    {
        $this->sut = new CanAccessTransactionWithId();

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
        $dto->shouldReceive('getId')->andReturn(76);

        $this->setIsValid('canAccessTransaction', [76], $isValid);

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
