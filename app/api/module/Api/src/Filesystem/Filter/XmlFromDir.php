<?php

namespace Dvsa\Olcs\Api\Filesystem\Filter;

use Symfony\Component\Finder\Finder;
use Zend\Filter\AbstractFilter;
use Zend\Filter\Exception;

/**
 * Class XmlFromDir
 * @package Olcs\Ebsr\Filter
 */
class XmlFromDir extends AbstractFilter
{
    /**
     * Returns the result of filtering $value
     *
     * @param  mixed $value
     * @throws Exception\RuntimeException If filtering $value is impossible
     * @return mixed
     */
    public function filter($value)
    {
        $finder = new Finder();
        $files = iterator_to_array($finder->files()->name('*.xml')->in($value));

        if (count($files) > 1) {
            throw new Exception\RuntimeException('There is more than one XML file in the pack');
        } elseif (!count($files)) {
            throw new Exception\RuntimeException('Could not find an XML file in the pack');
        }

        $xml = key($files);

        return $xml;
    }
}
