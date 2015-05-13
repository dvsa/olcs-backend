<?php

namespace Dvsa\Olcs\Api\Entity\Application;

use Doctrine\ORM\Mapping as ORM;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;

/**
 * Application Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="application",
 *    indexes={
 *        @ORM\Index(name="ix_application_licence_id", columns={"licence_id"}),
 *        @ORM\Index(name="ix_application_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_application_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_application_licence_type", columns={"licence_type"}),
 *        @ORM\Index(name="ix_application_status", columns={"status"}),
 *        @ORM\Index(name="ix_application_interim_status", columns={"interim_status"}),
 *        @ORM\Index(name="ix_application_withdrawn_reason", columns={"withdrawn_reason"}),
 *        @ORM\Index(name="ix_application_goods_or_psv", columns={"goods_or_psv"})
 *    }
 * )
 */
class Application extends AbstractApplication
{
    const ERROR_NI_NON_GOODS = 'AP-TOL-1';
    const ERROR_GV_NON_SR = 'AP-TOL-2';
    const ERROR_VAR_UNCHANGE_NI = 'AP-TOL-3';
    const ERROR_VAR_UNCHANGE_OT = 'AP-TOL-4';
    const ERROR_REQUIRES_CONFIRMATION = 'AP-TOL-5';

    public function updateTypeOfLicence($niFlag, $goodsOrPsv, $licenceType)
    {
        if ($this->validate($niFlag, $goodsOrPsv, $licenceType)) {
            $this->setNiFlag($niFlag);
            $this->setGoodsOrPsv($goodsOrPsv);
            $this->setLicenceType($licenceType);
            return true;
        }
    }

    public function validate($niFlag, $goodsOrPsv, $licenceType)
    {
        $errors = [];

        if ($niFlag === 'Y' && $goodsOrPsv->getId() === Licence::LICENCE_CATEGORY_PSV) {
            $errors['goodsOrPsv'][] =[
                self::ERROR_NI_NON_GOODS => 'NI can only apply for goods licences'
            ];
        }

        if ($goodsOrPsv->getId() === Licence::LICENCE_CATEGORY_GOODS_VEHICLE
            && $licenceType->getId() === Licence::LICENCE_TYPE_SPECIAL_RESTRICTED) {
            $errors['licenceType'][] =[
                self::ERROR_GV_NON_SR => 'GV operators cannot apply for special restricted licences'
            ];
        }

        if ($this->getIsVariation()) {
            if ($this->getGoodsOrPsv() != $goodsOrPsv) {
                $errors['goodsOrPsv'][] =[
                    self::ERROR_GV_NON_SR => 'GV operators cannot apply for special restricted licences'
                ];
            }

            if ($this->getNiFlag() != $niFlag) {
                $errors['niFlag'][] =[
                    self::ERROR_GV_NON_SR => 'GV operators cannot apply for special restricted licences'
                ];
            }
        }

        if (empty($errors)) {
            return true;
        }

        throw new ValidationException($errors);
    }
}
