<?php

namespace OlcsTest\Db\Service\Search;

use Olcs\Db\Service\Search\QueryTemplate;
use Mockery as m;
use ZfcRbac\Service\AuthorizationService;

/**
 * Class QueryTemplateTest
 */
class QueryTemplateTest extends m\Adapter\Phpunit\MockeryTestCase
{
    public function testQueryTemplateMissing()
    {
        $this->setExpectedException(\RuntimeException::class, "Query template file 'foo.json' is missing");
        $sut = new QueryTemplate('foo.json', 'bar');
        // prevent unused variable violation
        unset($sut);
    }

    public function testQueryTemplate()
    {
        $sut = new QueryTemplate(__DIR__ .'/mock-query-template.json', 'SMITH');
        $this->assertEquals(['query' => 'SMITH'], $sut->getParam('query'));
    }

    public function testQueryTemplateExtendedChars()
    {
        $sut = new QueryTemplate(__DIR__ .'/mock-query-template.json', 'SM"\das\'[]{}ITH');
        $this->assertEquals(['query' => 'SM"\das\'[]{}ITH'], $sut->getParam('query'));
    }
}
