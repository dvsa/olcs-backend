<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryPartial;

use Dvsa\Olcs\Api\Domain\QueryPartial\WithCreatedBy;
use Dvsa\Olcs\Api\Domain\QueryPartial\With;
use Mockery as m;

/**
 * WithCreatedTest
 */
class WithCreatedTest extends QueryPartialTestCase
{
    /** @var m\Mock */
    private $with;

    public function setUp(): void
    {
        // Cannot mock With as it is Final
        $this->with = new With();
        $this->sut = new WithCreatedBy($this->with);

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
                'SELECT a, u, cd, p FROM foo a LEFT JOIN a.createdBy u LEFT JOIN u.contactDetails cd ' .
                    'LEFT JOIN cd.person p',
                []
            ],
            [
                'SELECT a, u, cd, p FROM foo a LEFT JOIN a.createdBy u LEFT JOIN u.contactDetails cd ' .
                    'LEFT JOIN cd.person p',
                ['ENTITY']
            ],
            [
                'SELECT a, u, cd, p FROM foo a LEFT JOIN ALIAS.createdBy u LEFT JOIN u.contactDetails cd ' .
                    'LEFT JOIN cd.person p',
                ['ENTITY', 'ALIAS']
            ],
        ];
    }
}
