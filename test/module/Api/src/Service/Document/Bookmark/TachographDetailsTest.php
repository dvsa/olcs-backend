<?php

namespace Dvsa\OlcsTest\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Service\Document\Bookmark\TachographDetails;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\Olcs\Api\Entity\Licence\Licence;

/**
 * TachographDetails bookmark test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class TachographDetailsTest extends MockeryTestCase
{
    public function testGetQuery()
    {
        $bookmark = new TachographDetails();
        $query = $bookmark->getQuery(['licence' => 123]);

        $this->assertInstanceOf(\Dvsa\Olcs\Transfer\Query\QueryInterface::class, $query);
    }

    public function testRenderWithNoTachographDetails()
    {
        $bookmark = new TachographDetails();
        $bookmark->setData([]);

        $this->assertEquals(
            '',
            $bookmark->render()
        );
    }

    public function testRenderWithTachographDetails()
    {
        $bookmark = m::mock('Dvsa\Olcs\Api\Service\Document\Bookmark\TachographDetails')
            ->makePartial()
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('getSnippet')
            ->with('TachographDetails')
            ->andReturn('snippet')
            ->once()
            ->getMock();

        $bookmark->setData(
            [
                'tachographIns' => [
                    'id' => Licence::TACH_INT
                ],
                'tachographInsName' => 'foo'
            ]
        );

        $content = [
            'Address' => 'foo',
            'checkbox1' => 'X',
            'checkbox2' => '',
            'checkbox3' => ''
        ];

        $mockParser = m::mock('Dvsa\Olcs\Api\Service\Document\Parser\RtfParser')
            ->shouldReceive('replace')
            ->with('snippet', $content)
            ->andReturn('content')
            ->once()
            ->getMock();

        $bookmark->setParser($mockParser);

        $this->assertEquals(
            'content',
            $bookmark->render()
        );
    }
}
