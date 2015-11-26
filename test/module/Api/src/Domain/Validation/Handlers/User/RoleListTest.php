<?php

/**
 * Role List Test
 */
namespace Dvsa\OlcsTest\Api\Domain\Validation\Handlers\User;

use Dvsa\Olcs\Api\Domain\Validation\Handlers\User\RoleList as Sut;
use Dvsa\Olcs\Transfer\Query\User\RoleList as Qry;
use Dvsa\OlcsTest\Api\Domain\Validation\Handlers\AbstractHandlerTestCase;

/**
 * Role List Test
 */
class RoleListTest extends AbstractHandlerTestCase
{
    protected $sut;

    public function setUp()
    {
        $this->sut = new Sut();

        parent::setUp();
    }

    public function testIsValid()
    {
        $dto = Qry::create([]);

        $this->assertEquals(true, $this->sut->isValid($dto));
    }
}
