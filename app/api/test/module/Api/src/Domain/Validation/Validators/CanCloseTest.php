<?php

/**
 * Can Close Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Validation\Validators;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\Validation\Validators\CanClose;
use Dvsa\Olcs\Api\Entity\CloseableInterface;

/**
 * Can Close Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class CanCloseTest extends AbstractValidatorsTestCase
{
    /**
     * @var CanClose
     */
    protected $sut;

    public function setUp()
    {
        $this->sut = new CanClose();

        parent::setUp();
    }

    /**
     * @dataProvider provider
     */
    public function testIsValidTrue($canClose, $expected)
    {
        $case = m::mock();
        $case->shouldReceive('getId')->andReturn(55);

        /** @var CloseableInterface $entity */
        $entity = m::mock(CloseableInterface::class);
        $entity->shouldReceive('canClose')->andReturn($canClose);

        $this->assertEquals($expected, $this->sut->isValid($entity));
    }

    public function provider()
    {
        return [
            [true, true],
            [false, false]
        ];
    }
}
