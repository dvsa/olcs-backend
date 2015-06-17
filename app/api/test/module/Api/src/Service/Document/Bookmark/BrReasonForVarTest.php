<?php
/**
 * Class
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Service\Document\Bookmark;

/**
 * Class
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
class BrReasonForVarTest extends \PHPUnit_Framework_TestCase
{
    const SUT_CLASS_NAME = '\Dvsa\Olcs\Api\Service\Document\Bookmark\BrReasonForVar';

    public function testGetQuery()
    {
        $sutClassName = static::SUT_CLASS_NAME;

        $bookmark = new $sutClassName();

        $this->assertInstanceOf(
            \Dvsa\Olcs\Transfer\Query\QueryInterface::class,
            $bookmark->getQuery(['busRegId' => 123])
        );
    }

    public function testRender()
    {
        $sutClassName = static::SUT_CLASS_NAME;

        $bookmark = new $sutClassName();

        $data = [
            'id' => '123',
            'variationReasons' => [
                0 => ['description' => 'LA 1'],
                1 => ['description' => 'L A2'],
            ]
        ];

        $bookmark->setData($data);

        $this->assertEquals('LA 1, L A2', $bookmark->render());
    }
}
