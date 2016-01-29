<?php
/**
 * Class
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Service\Document\Bookmark\StatementContactType as Sut;

/**
 * Class
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
class StatementContactTypeTest extends \PHPUnit_Framework_TestCase
{
    public function testGetQuery()
    {
        $id = '123';

        $bookmark = new Sut();

        $query = $bookmark->getQuery(['statement' => $id]);

        $this->assertInstanceOf(\Dvsa\Olcs\Transfer\Query\QueryInterface::class, $query);
    }

    public function testRender()
    {
        $bookmark = new Sut();

        $data = [
            'id' => '123',
            'contactType' => [
                'description' => 'Value 1'
            ]
        ];

        $bookmark->setData($data);

        $this->assertEquals('Value 1', $bookmark->render());
    }
}
