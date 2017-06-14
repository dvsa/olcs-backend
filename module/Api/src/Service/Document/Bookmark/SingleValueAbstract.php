<?php
/**
 * SingleValueAbstract
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Service\Document\Bookmark\Base\DynamicBookmark;
use Dvsa\Olcs\Api\Service\Document\Bookmark\Formatter;

/**
 * SingleValueAbstract
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
abstract class SingleValueAbstract extends DynamicBookmark
{
    const CLASS_NAMESPACE = __NAMESPACE__; // do not change/override this.
    const FORMATTER = null; // defaults to null
    const FIELD = null; // example
    const SRCH_FLD_KEY = 'id'; // example
    const SRCH_VAL_KEY = 'busRegId'; // example
    const DEFAULT_VALUE = null;
    const QUERY_CLASS = null;
    const BUNDLE = [];

    public function getQuery(array $data)
    {
        $data = [
            static::SRCH_FLD_KEY => isset($data[static::SRCH_VAL_KEY]) ? $data[static::SRCH_VAL_KEY] : null,
            'bundle' => static::BUNDLE
        ];

        $queryClass = static::QUERY_CLASS;

        return $queryClass::create($data);
    }

    public function render()
    {
        $value = isset($this->data[static::FIELD]) ? $this->data[static::FIELD] : null;

        $formatter = static::FORMATTER;

        if (!is_null($value) && !is_null($formatter)) {

            /**
             * @var \Dvsa\Olcs\Api\Service\Document\Bookmark\Formatter\Date $class
             */
            $class = __NAMESPACE__ . '\Formatter\\' . $formatter;

            $value = $class::format((array)$value);
        }

        if (empty($value) && static::DEFAULT_VALUE !== null) {
            $value = static::DEFAULT_VALUE;
        }

        return $value;
    }
}
