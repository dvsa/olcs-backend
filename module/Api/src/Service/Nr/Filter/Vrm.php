<?php

namespace Dvsa\Olcs\Api\Service\Nr\Filter;

use Zend\ServiceManager\FactoryInterface;
use Zend\Filter\AbstractFilter as ZendAbstractFilter;
use Dvsa\Olcs\Transfer\Filter\Vrm as VrmFilter;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class Vrm
 * @package Dvsa\Olcs\Api\Service\Nr\Filter
 */
class Vrm extends ZendAbstractFilter
{
    /**
     * @var VrmFilter
     */
    private $vrmFilter;

    /**
     * @return VrmFilter
     */
    public function getVrmFilter()
    {
        return $this->vrmFilter;
    }

    /**
     * @param VrmFilter $vrmFilter
     */
    public function setVrmFilter($vrmFilter)
    {
        $this->vrmFilter = $vrmFilter;
    }

    /**
     * Returns the result of filtering $value
     *
     * @param  array $value
     * @return array
     */
    public function filter($value)
    {
        $value['vrm'] = $this->getVrmFilter()->filter($value['vrm']);
        return $value;
    }
}
