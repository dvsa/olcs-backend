<?php

namespace Dvsa\OlcsTest\Api\Service\Document\Bookmark;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\Olcs\Api\Service\Document\Bookmark\FeeReqNumber;

/**
 * Fee Req Number test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class FeeReqNumberTest extends MockeryTestCase
{
    public function testGetQuery()
    {
        $bookmark = new FeeReqNumber();
        $query = $bookmark->getQuery(['fee' => 123]);

        $this->assertInstanceOf(\Dvsa\Olcs\Transfer\Query\QueryInterface::class, $query);
    }

    public function testRender()
    {
        $bookmark = new FeeReqNumber();
        $bookmark->setData(
            [
                'licence' => [
                    'licNo' => '123'
                ],
                'id' => 2
            ]
        );

        $this->assertEquals('123/2', $bookmark->render());
    }
}
