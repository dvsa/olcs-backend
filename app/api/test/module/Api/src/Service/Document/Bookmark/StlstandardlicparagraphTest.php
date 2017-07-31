<?php

namespace Dvsa\OlcsTest\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Domain\Query\Bookmark\ApplicationBundle;
use Dvsa\Olcs\Api\Domain\Query\Bookmark\LicenceBundle;
use Dvsa\Olcs\Api\Domain\Query\Bookmark\Applicationtype;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Service\Document\Bookmark\Stlstandardlicparagraph as Sut;
use Mockery as m;

/**
 * StlstandardlicparagraphTest
 */
class StlstandardlicparagraphTest extends \PHPUnit_Framework_TestCase
{
    public function testGetQueryLicence()
    {
        $bookmark = new Sut();
        $query = $bookmark->getQuery(['licence' => 123]);

        $this->assertInstanceOf(LicenceBundle::class, $query);
        $this->assertSame(123, $query->getId());
        $this->assertSame([], $query->getBundle());
    }

    public function testGetQueryApplication()
    {
        $bookmark = new Sut();
        $query = $bookmark->getQuery(['application' => 456]);

        $this->assertInstanceOf(ApplicationBundle::class, $query);
        $this->assertSame(456, $query->getId());
        $this->assertSame([], $query->getBundle());
    }

    /**
     * @dataProvider testRenderDataProvider
     */
    public function testRender($expectSnippet, $licenceTypeId)
    {
        $bookmark = new Sut();
        $bookmark->setData(['licenceType' => ['id' => $licenceTypeId]]);

        if ($expectSnippet) {
            $mockParser = m::mock();
            $mockParser->shouldReceive('getFileExtension')->with()->once()->andReturn('rtf');
            $bookmark->setParser($mockParser);
            $this->assertStringStartsWith('(If a standard licence – delete as appropriate)', $bookmark->render());
        } else {
            $this->assertNull($bookmark->render());
        }
    }

    public function testRenderDataProvider()
    {
        return [
            [true, Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL],
            [true, Licence::LICENCE_TYPE_STANDARD_NATIONAL],
            [false, Licence::LICENCE_TYPE_RESTRICTED],
            [false, Licence::LICENCE_TYPE_SPECIAL_RESTRICTED],
        ];
    }
}
