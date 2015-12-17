<?php

namespace Dvsa\Olcs\Api\Domain;

/**
 * NationalRegisterAwareInterface
 */
interface NationalRegisterAwareInterface
{
    /**
     * @param array $nationalRegisterConfig
     */
    public function setNationalRegisterConfig(array $nationalRegisterConfig);

    /**
     * @return array national register configuration
     */
    public function getNationalRegisterConfig();
}
