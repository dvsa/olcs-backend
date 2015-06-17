<?php
/**
 * Class
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Service\Document\Bookmark\StatementNameBodyAddress as Sut;

/**
 * Class
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
class StatementNameBodyAddressTest extends \PHPUnit_Framework_TestCase
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
            'requestorsBody' => 'Some Body or Business',
            'requestorsContactDetails' => [
                'person' => [
                    'forename' => 'James',
                    'familyName' => 'Smith'
                ],
                'address' => [
                    'addressLine1' => 'A1',
                    'addressLine2' => 'A2',
                    'addressLine3' => 'A3',
                    'addressLine4' => 'A4',
                    'town' => 'A5',
                    'postcode' => 'A6'
                ]
            ]
        ];

        $bookmark->setData($data);

        $result = "James Smith\nSome Body or Business\nA1\nA2\nA3\nA4\nA5\nA6";

        $this->assertEquals($result, $bookmark->render());
    }
}
