<?php

namespace Autoload;

use Composer\Autoload\ClassLoader;

/**
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CustomClassLoader extends ClassLoader
{
    private $classMap = array();

    private $dynamicClassMap = array();

    private $dirtyMap = false;

    /**
     * @param array $classMap Class to filename map
     */
    public function addClassMap(array $classMap)
    {
        if ($this->classMap) {
            $this->classMap = array_merge($this->classMap, $classMap);
        } else {
            $this->classMap = $classMap;
        }
    }

    /**
     * @param array $classMap Class to filename map
     */
    public function addDynamicClassMap(array $classMap)
    {
        $this->addClassMap($classMap);
        $this->dynamicClassMap = array_merge($this->dynamicClassMap, $classMap);
    }


    /**
     * Add to the original class map
     *  then add to our own, so we know which
     */
    protected function addToDynamicClassMap($class, $file)
    {
        $this->dirtyMap = true;
        $this->classMap[$class] = $file;
        $this->dynamicClassMap[$class] = $file;
    }

    /**
     * Finds the path to the file where the class is defined.
     *
     * @param string $class The name of the class
     *
     * @return string|false The path if found, false otherwise
     */
    public function findFile($class)
    {
        // work around for PHP 5.3.0 - 5.3.2 https://bugs.php.net/50731
        if ('\\' == $class[0]) {
            $class = substr($class, 1);
        }

        // class map lookup
        if (isset($this->classMap[$class])) {
            return $this->classMap[$class];
        }

        $file = parent::findFile($class);

        $this->addToDynamicClassMap($class, $file);

        return $file;
    }

    public function __destruct()
    {
        /**
         * This should never happen in production, only in development
         */
        if ($this->dirtyMap) {
            $rootPath = realpath(dirname(__DIR__));
            $content = '<?php

$rootPath = realpath(dirname(__DIR__));

return array(';
            foreach ($this->dynamicClassMap as $class => $path) {

                if (strstr($path, $rootPath)) {
                    $path = '$rootPath . \'' . str_replace($rootPath, '', $path) . '\'';
                } else {
                    $path = '\'' . $path . '\'';
                }
                $content .= '
    \'' . $class . '\' => ' . $path . ',';
            }

            $content .= '
);';
            file_put_contents(__DIR__ . '/classmap.php', $content);
        }
    }
}
