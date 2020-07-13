<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryPartial;

use Dvsa\Olcs\Api\Domain\QueryPartial\Order;

/**
 * OrderTest
 */
class OrderTest extends QueryPartialTestCase
{
    /**
     * @var Order
     */
    protected $sut;

    public function setUp(): void
    {
        $this->sut = new Order();

        parent::setUp();
    }

    /**
     * @dataProvider dataProvider
     */
    public function testModifyQuery($expectedDql, $arguments)
    {
        $this->sut->modifyQuery($this->qb, $arguments);
        $this->assertSame(
            $expectedDql,
            $this->qb->getDQL()
        );
    }

    public function dataProvider()
    {
        return [
            [
                'SELECT a FROM foo a ORDER BY a.PROP DESC',
                ['PROP', 'DESC']
            ],
            [
                'SELECT a FROM foo a ORDER BY ENTITY.PROP ASC',
                ['ENTITY.PROP', 'ASC']
            ],
            [
                'SELECT a FROM foo a ORDER BY PROP ASC',
                ['PROP', 'ASC', ['XXXX', 'PROP']]
            ],
            [
                'SELECT a FROM foo a ORDER BY ENTITY.PROP ASC',
                ['ENTITY.PROP', 'ASC', ['XXXX', 'ENTITY.PROP']]
            ],
        ];
    }
}
