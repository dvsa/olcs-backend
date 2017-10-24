<?php

/**
 * WithPersonContactDetails Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryPartial;

use Dvsa\Olcs\Api\Domain\QueryPartial\With;
use Dvsa\Olcs\Api\Domain\QueryPartial\WithPersonContactDetails;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\QueryPartial\QueryPartialInterface;

/**
 * WithPersonContactDetails Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class WithPersonContactDetailsTest extends QueryPartialTestCase
{
    /** @var m\Mock */
    private $with;

    public function setUp()
    {
        // Cannot mock With as it is Final
        $this->with = new With();
        $this->sut = new WithPersonContactDetails($this->with);

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
                'SELECT a, c, p, a, ct, pc FROM foo a LEFT JOIN a.contactDetails c LEFT JOIN c.person p '.
                    'LEFT JOIN c.address a LEFT JOIN c.contactType ct LEFT JOIN c.phoneContacts pc',
                []
            ],
            [
                'SELECT a, c, p, a, ct, pc FROM foo a LEFT JOIN a.PROP c LEFT JOIN c.person p '.
                    'LEFT JOIN c.address a LEFT JOIN c.contactType ct LEFT JOIN c.phoneContacts pc',
                ['PROP']
            ],
            [
                'SELECT a, c, p, a, ct, pc FROM foo a LEFT JOIN ENTITY.PROP c LEFT JOIN c.person p '.
                    'LEFT JOIN c.address a LEFT JOIN c.contactType ct LEFT JOIN c.phoneContacts pc',
                ['ENTITY.PROP']
            ],
        ];
    }
}
