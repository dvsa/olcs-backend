<?php

namespace Dvsa\OlcsTest\Api\Domain\Validation\Validators;

use Dvsa\Olcs\Api\Domain\Validation\Validators\CanAccessTransaction;
use Dvsa\Olcs\Api\Entity\Fee\Transaction;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Mockery as m;

/**
 * @covers CanAccessTransaction
 */
class CanAccessTransactionTest extends AbstractValidatorsTestCase
{
    /**
     * @var CanAccessTransaction
     */
    protected $sut;

    public function setUp()
    {
        $this->sut = new CanAccessTransaction();

        parent::setUp();
    }

    public function testIsValidId()
    {
        $this->setIsGranted(Permission::INTERNAL_USER, false);
        $entity = m::mock(Transaction::class);

        $repo = $this->mockRepo('Transaction');
        $repo->shouldReceive('fetchById')->with(111)->andReturn($entity);

        $this->setIsValid('isOwner', [$entity], true);

        $this->assertEquals(true, $this->sut->isValid(111));
    }

    public function testIsValidReference()
    {
        $this->setIsGranted(Permission::INTERNAL_USER, false);
        $entity = m::mock(Transaction::class);

        $repo = $this->mockRepo('Transaction');
        $repo->shouldReceive('fetchByReference')->with('ABC1')->andReturn($entity);

        $this->setIsValid('isOwner', [$entity], true);

        $this->assertEquals(true, $this->sut->isValid('ABC1'));
    }
}
