<?php

/**
 * Can Access Xoc With Reference Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Validation\Handlers\OperatingCentre;

use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\OlcsTest\Api\Domain\Validation\Handlers\AbstractHandlerTestCase;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\CorrespondenceInbox\CanAccessCorrespondenceInboxWithId;

/**
 * @covers CanAccessCorrespondenceInboxWithId
 */
class CanAccessCorrespondenceInboxWithIdTest extends AbstractHandlerTestCase
{
    /**
     * @var CanAccessCorrespondenceInboxWithId
     */
    protected $sut;

    public function setUp()
    {
        $this->sut = new CanAccessCorrespondenceInboxWithId();

        parent::setUp();
    }

    /**
     * @dataProvider provider
     *
     * @param $canAccess
     * @param $expected
     */
    public function testIsValid($canAccess, $expected)
    {
        /** @var CommandInterface $dto */
        $dto = m::mock(CommandInterface::class);
        $dto->shouldReceive('getId')->andReturn(76);

        $this->setIsValid('canAccessCorrespondenceInbox', [76], $canAccess);

        $this->assertSame($expected, $this->sut->isValid($dto));
    }

    public function provider()
    {
        return [
            [true, true],
            [false, false],
        ];
    }
}
