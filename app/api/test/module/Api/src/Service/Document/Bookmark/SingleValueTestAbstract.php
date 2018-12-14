<?php
/**
 * Class
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Service\Document\Bookmark\Formatter\FormatterInterface;

/**
 * Class
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
abstract class SingleValueTestAbstract extends \PHPUnit\Framework\TestCase
{
    /**
     * Implement this in the child class
     */
    const SUT_CLASS_NAME = '\Dvsa\Olcs\Api\Service\Document\Bookmark\BOOKMARK_CLASS_NAME';

    public function testGetQuery()
    {
        $sutClassName = static::SUT_CLASS_NAME;

        $id = '123';

        $bookmark = new $sutClassName();

        $query = $bookmark->getQuery([$sutClassName::SRCH_VAL_KEY => $id]);

        $this->assertInstanceOf(\Dvsa\Olcs\Transfer\Query\QueryInterface::class, $query);
    }

    public function testRender()
    {
        $sutClassName = static::SUT_CLASS_NAME;

        $bookmark = new $sutClassName();

        $key = $sutClassName::FIELD;
        $value = get_class($bookmark);

        $bookmark->setData([$key => $value]);

        $formatter = $this->getFormatter();
        if ($formatter instanceof FormatterInterface) {

            $formatterName = get_class($formatter);

            $this->assertEquals(
                $formatterName::format((array)$value),
                $bookmark->render()
            );

        } else {
            $this->assertEquals($value, $bookmark->render());
        }
    }

    /**
     * tests the default value is rendered correctly in those bookmarks that populate it
     * (base class has a default value of null)
     */
    public function testRenderDefaultValue()
    {
        $sutClassName = static::SUT_CLASS_NAME;
        $bookmark = new $sutClassName();
        $this->assertEquals($sutClassName::DEFAULT_VALUE, $bookmark->render());
    }

    public function getFormatter()
    {
        $sutClassName = static::SUT_CLASS_NAME;

        if (is_null($sutClassName::FORMATTER)) {
            return false;
        }

        $formatterClassName = $sutClassName::CLASS_NAMESPACE . '\Formatter\\' . $sutClassName::FORMATTER;

        return new $formatterClassName();
    }
}
