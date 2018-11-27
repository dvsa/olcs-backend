<?php
/**
 * Class
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Service\Document\Bookmark as BookmarkNs;

/**
 * Class
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
class S9RequestorNameBodyAddressTest extends \PHPUnit\Framework\TestCase
{
    public function testAlias()
    {
        $sut = new BookmarkNs\S9RequestorNameBodyAddress();

        $this->assertInstanceOf('Dvsa\Olcs\Api\Service\Document\Bookmark\StatementNameBodyAddress', $sut);
    }
}
