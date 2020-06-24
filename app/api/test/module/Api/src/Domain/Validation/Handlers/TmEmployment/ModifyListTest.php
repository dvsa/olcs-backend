<?php

namespace Dvsa\OlcsTest\Api\Domain\Validation\Handlers\TmEmployment;

use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\OlcsTest\Api\Domain\Validation\Handlers\AbstractHandlerTestCase;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\TmEmployment\ModifyList;

/**
 * ModifyList TmEmployment test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class ModifyListTest extends AbstractHandlerTestCase
{
    /**
     * @var ModifyList
     */
    protected $sut;

    public function setUp(): void
    {
        $this->sut = new ModifyList();

        parent::setUp();
    }

    public function testIsValidInternalUser()
    {
        /** @var CommandInterface $dto */
        $dto = m::mock(CommandInterface::class);

        $this->auth->shouldReceive('isGranted')
            ->with(\Dvsa\Olcs\Api\Entity\User\Permission::INTERNAL_USER, null)->once()
            ->andReturn(true);

        $this->assertSame(true, $this->sut->isValid($dto));
    }

    /**
     * @dataProvider provider
     */
    public function testIsValid($canAccess, $expected)
    {
        /** @var CommandInterface $dto */
        $dto = m::mock(CommandInterface::class);
        $dto->shouldReceive('getIds')->andReturn([1, 2]);

        $this->auth->shouldReceive('isGranted')
            ->with(\Dvsa\Olcs\Api\Entity\User\Permission::INTERNAL_USER, null)->once()
            ->andReturn(false);

        $mockValidator = m::mock();
        $this->validatorManager->setService('canAccessTmEmployment', $mockValidator);

        $mockValidator->shouldReceive('isValid')->with(1)->andReturn($canAccess);
        $mockValidator->shouldReceive('isValid')->with(2)->andReturn($canAccess);

        $this->assertSame($expected, $this->sut->isValid($dto));
    }
    /**
     * @return array
     */
    public function provider()
    {
        return [
            [true, true],
            [false, false],
        ];
    }
}
