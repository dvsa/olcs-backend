<?php
/**
 * A trait that classes can use to easily get/set the language.
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
namespace Olcs\Db\Traits;

/**
 * A trait that classes can use to easily get/set the language.
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
trait LanguageAwareTrait
{
    /**
     * @var string
     */
    protected $language;

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param string $language
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }
}
