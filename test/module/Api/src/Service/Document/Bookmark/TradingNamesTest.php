<?php
namespace Dvsa\OlcsTest\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Service\Document\Bookmark\TradingNames;

/**
 * Trading Names test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class TradingNamesTest extends \PHPUnit_Framework_TestCase
{
    public function testGetQuery()
    {
        $bookmark = new TradingNames();
        $query = $bookmark->getQuery(['licence' => 123]);

        $this->assertInstanceOf(\Dvsa\Olcs\Transfer\Query\QueryInterface::class, $query);
    }

    public function testRenderWithNoTradingNames()
    {
        $bookmark = new TradingNames();
        $bookmark->setData(
            [
                'organisation' => null
            ]
        );

        $this->assertEquals(
            '',
            $bookmark->render()
        );
    }

    public function testRenderWithTradingNames()
    {
        $bookmark = new TradingNames();
        $bookmark->setData(
            [
                'organisation' => [
                    'tradingNames' => [
                        ['name' => 'tn1'],
                        ['name' => 'tn2'],
                        ['name' => 'tn3']
                    ]
                ]
            ]
        );

        $this->assertEquals(
            'tn1, tn2, tn3',
            $bookmark->render()
        );
    }
}
