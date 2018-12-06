<?php

/**
 * Expression Builder Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace OlcsTest\Db\Utility;

use PHPUnit_Framework_TestCase;
use Olcs\Db\Utility\ExpressionBuilder;
use Doctrine\ORM\QueryBuilder;
use \Doctrine\ORM\Query\Expr;

/**
 * Expression Builder Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ExpressionBuilderTest extends PHPUnit_Framework_TestCase
{
    protected $em;
    protected $qb;
    protected $sut;

    protected function setUp()
    {
        $this->em = $this->createPartialMock(
            'Doctrine\ORM\EntityManager',
            array('getExpressionBuilder', 'getClassMetadata')
        );

        $this->em->expects($this->any())
            ->method('getExpressionBuilder')
            ->will($this->returnValue(new Expr));

        $this->qb = new QueryBuilder($this->em);

        $this->sut = new ExpressionBuilder();
        $this->sut->setQueryBuilder($this->qb);
        $this->sut->setEntityManager($this->em);
        $this->sut->setEntity('Foo');
    }

    /**
     * @group expression_builder
     */
    public function testSetParams()
    {
        $params = array('foo' => 'bar');

        $this->sut->setParams($params);

        $this->assertEquals($params, $this->sut->getParams());
    }

    /**
     * @group expression_builder
     */
    public function testBuildWhereExpressionWithEmptyQuery()
    {
        $query = array();

        $expression = $this->sut->buildWhereExpression($query);

        $this->assertNull($expression);
    }

    /**
     * @group expression_builder
     * @dataProvider whereExpressionProvider
     */
    public function testBuildWhereExpression($query, $expectedExpression, $expectedParams)
    {
        $expression = $this->sut->buildWhereExpression($query);
        $params = $this->sut->getParams();

        $this->assertEquals($expectedExpression, $expression->__toString());
        $this->assertEquals($expectedParams, $params);
    }

    public function whereExpressionProvider()
    {
        return array(
            'int' => array(
                array('foo1' => 3),
                'a.foo1 = ?0',
                array(3)
            ),
            'null' => array(
                array('foo1' => 'NULL'),
                'a.foo1 IS NULL',
                array()
            ),
            'not null' => array(
                array('foo1' => 'NOT NULL'),
                'a.foo1 IS NOT NULL',
                array()
            ),
            'in' => array(
                array('foo1' => 'IN ["foo","bar","cake"]'),
                'a.foo1 IN(\'foo\', \'bar\', \'cake\')',
                array()
            ),
            'not in' => array(
                array('foo1' => 'NOT IN ["foo","bar","cake"]'),
                'a.foo1 NOT IN(\'foo\', \'bar\', \'cake\')',
                array()
            ),
            'lte' => array(
                array('foo1' => '<= 10'),
                'a.foo1 <= ?0',
                array(10)
            ),
            'lt' => array(
                array('foo1' => '< 10'),
                'a.foo1 < ?0',
                array(10)
            ),
            'eq' => array(
                array('foo1' => '= 10'),
                'a.foo1 = ?0',
                array(10)
            ),
            'gte' => array(
                array('foo1' => '>= 10'),
                'a.foo1 >= ?0',
                array(10)
            ),
            'gt' => array(
                array('foo1' => '> 10'),
                'a.foo1 > ?0',
                array(10)
            ),
            'like' => array(
                array('foo1' => '~10'),
                'a.foo1 LIKE ?0',
                array(10)
            ),
            'not like' => array(
                array('foo1' => '!~10'),
                'a.foo1 NOT LIKE ?0',
                array(10)
            ),
            'not equal' => array(
                array('foo1' => '!= 10'),
                'a.foo1 <> ?0',
                array(10)
            ),
            'equal string' => array(
                array('foo1' => 'bar'),
                'a.foo1 = ?0',
                array('bar')
            ),
            'multiple conditions' => array(
                array('foo1' => 'bar', 'foo2' => 'bar2'),
                'a.foo1 = ?0 AND a.foo2 = ?1',
                array('bar', 'bar2')
            ),
            'or conditions' => array(
                array('foo1' => array(10, 20)),
                'a.foo1 = ?0 OR a.foo1 = ?1',
                array(10, 20)
            ),
            'and (and) or conditions' => array(
                array('foo1' => array(10, 20), 'foo2' => 'bar'),
                '(a.foo1 = ?0 OR a.foo1 = ?1) AND a.foo2 = ?2',
                array(10, 20, 'bar')
            ),
            'complex one' => array(
                array(
                    'foo1' => array(
                        array(
                            '>= 5',
                            '<= 10'
                        ),
                        0
                    ), 'foo2' => 'bar'),
                '((a.foo1 >= ?0 AND a.foo1 <= ?1) OR a.foo1 = ?2) AND a.foo2 = ?3',
                array(5, 10, 0, 'bar')
            ),
            'complex or one' => array(
                array(
                    array(
                        'foo' => 'bar',
                        'bar' => 'cake'
                    )
                ),
                'a.foo = ?0 OR a.bar = ?1',
                array('bar', 'cake')
            ),
        );
    }
}
