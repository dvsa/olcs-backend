<?php

namespace Dvsa\OlcsTest\Api\Domain\Validation\Validators;


use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\CanDeleteSurrender;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class CanDeleteSurrenderTest extends MockeryTestCase
{
    /**
     * @dataProvider provider
     */
    public function testIsValid($isInternalUser, $isSystemUser, $expected)
    {
        $sut = m::mock(CanDeleteSurrender::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $dto = m::mock(CommandInterface::class);

        $sut->shouldReceive('isInternalUser')->with()->andReturn($isInternalUser);
        $sut->shouldReceive('isSystemUser')->with()->andReturn($isSystemUser);

        /** @var CanDeleteSurrender $sut */
        $this->assertSame($expected, $sut->isValid($dto));
    }

    public function provider()
    {
        return [
            'case_01' => [
                'isInternalUser' => true,
                'isSystemUser' => true,
                'expected' => true
            ],
            'case_02' => [
                'isInternalUser' => false,
                'isSystemUser' => true,
                'expected' => true
            ],
            'case_03' => [
                'isInternalUser' => true,
                'isSystemUser' => false,
                'expected' => true
            ],
            'case_04' => [
                'isInternalUser' => false,
                'isSystemUser' => false,
                'expected' => false
            ],
        ];
    }

}
