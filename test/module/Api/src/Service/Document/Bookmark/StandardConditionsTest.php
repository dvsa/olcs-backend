<?php

namespace Dvsa\OlcsTest\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Service\Document\Bookmark\StandardConditions;
use Dvsa\Olcs\Api\Entity\Licence\Licence;

/**
 * TA Name test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class StandardConditionsTest extends \PHPUnit_Framework_TestCase
{
    public function testGetQuery()
    {
        $bookmark = new StandardConditions();
        $query = $bookmark->getQuery(['licence' => 123]);

        $this->assertInstanceOf(\Dvsa\Olcs\Transfer\Query\QueryInterface::class, $query);
    }

    /**
     * @dataProvider renderDataProvider
     */
    public function testRender($niFlag, $licenceType, $path)
    {
        $bookmark = $this->createPartialMock(StandardConditions::class, ['getSnippet']);

        $bookmark->expects($this->any())
            ->method('getSnippet')
            ->with($path)
            ->willReturn('snippet');

        $bookmark->setData(
            [
                'niFlag' => $niFlag,
                'licenceType' => [
                    'id' => $licenceType
                ]
            ]
        );

        $this->assertEquals('snippet', $bookmark->render());
    }

    public function renderDataProvider()
    {
        return [
            [
                'N',
                Licence::LICENCE_TYPE_RESTRICTED,
                'GB_RESTRICTED_LICENCE_CONDITIONS'
            ], [
                'N',
                Licence::LICENCE_TYPE_STANDARD_NATIONAL,
                'GB_STANDARD_LICENCE_CONDITIONS'
            ], [
                'N',
                Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL,
                'GB_STANDARD_INT_LICENCE_CONDITIONS'
            ], [
                'Y',
                Licence::LICENCE_TYPE_RESTRICTED,
                'NI_RESTRICTED_LICENCE_CONDITIONS'
            ], [
                'Y',
                Licence::LICENCE_TYPE_STANDARD_NATIONAL,
                'NI_STANDARD_LICENCE_CONDITIONS'
            ], [
                'Y',
                Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL,
                'NI_STANDARD_INT_LICENCE_CONDITIONS'
            ]
        ];
    }
}
