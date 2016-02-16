<?php

namespace Dvsa\Olcs\Api\Service\Nr\Validator;

use Zend\ServiceManager\FactoryInterface;
use Zend\Validator\Exception;
use Zend\Validator\AbstractValidator as ZendAbstractValidator;
use Dvsa\Olcs\Transfer\Validators\Vrm as VrmValidator;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class Vrm
 * @package Dvsa\Olcs\Api\Service\Nr\Validator
 */
class Vrm extends ZendAbstractValidator
{
    const VRM_FORMAT_ERROR = 'vrm-format-error';

    /**
     * @var array
     */
    protected $messageTemplates = [
        self::VRM_FORMAT_ERROR => 'VRM is not in the correct format',
    ];

    /**
     * @var VrmValidator
     */
    private $vrmValidator;

    /**
     * @return VrmValidator
     */
    public function getVrmValidator()
    {
        return $this->vrmValidator;
    }

    /**
     * @param VrmValidator $vrmValidator
     */
    public function setVrmValidator($vrmValidator)
    {
        $this->vrmValidator = $vrmValidator;
    }

    /**
     * Returns whether the VRM is valid
     *
     * @param mixed $value
     * @return bool
     */
    public function isValid($value)
    {
        $validator = $this->getVrmValidator();

        if (!$validator->isValid($value['vrm'])) {
            $this->error(self::VRM_FORMAT_ERROR);
            return false;
        }

        return true;
    }
}
