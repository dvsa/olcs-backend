<?php

namespace Dvsa\Olcs\Api\Entity\Application;

use Doctrine\ORM\Mapping as ORM;
use Dvsa\Olcs\Api\Entity\System\RefData;
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

    const APPLICATION_STATUS_NOT_SUBMITTED = 'apsts_not_submitted';
    const APPLICATION_STATUS_GRANTED = 'apsts_granted';
    const APPLICATION_STATUS_UNDER_CONSIDERATION = 'apsts_consideration';
    const APPLICATION_STATUS_VALID = 'apsts_valid';
    const APPLICATION_STATUS_WITHDRAWN = 'apsts_withdrawn';
    const APPLICATION_STATUS_REFUSED = 'apsts_refused';
    const APPLICATION_STATUS_NOT_TAKEN_UP = 'apsts_ntu';

    const INTERIM_STATUS_REQUESTED = 'int_sts_requested';
    const INTERIM_STATUS_INFORCE = 'int_sts_in_force';
    const INTERIM_STATUS_REFUSED = 'int_sts_refused';
    const INTERIM_STATUS_REVOKED = 'int_sts_revoked';
    const INTERIM_STATUS_GRANTED = 'int_sts_granted';

    public function __construct(Licence $licence, RefData $status, $isVariation)
    {
        parent::__construct();

        $this->setLicence($licence);
        $this->setStatus($status);
        $this->setIsVariation($isVariation);
    }

    public function updateTypeOfLicence($niFlag, $goodsOrPsv, $licenceType)
    {
        if ($this->validateTol($niFlag, $goodsOrPsv, $licenceType)) {
            $this->setNiFlag($niFlag);
            $this->setGoodsOrPsv($goodsOrPsv);
            $this->setLicenceType($licenceType);
            return true;
        }
    }

    public function isValidTol($niFlag, $goodsOrPsv, $licenceType)
    {
        try {
            return $this->validateTol($niFlag, $goodsOrPsv, $licenceType);
        } catch (ValidationException $ex) {
            return false;
        }
    }

    public function validateTol($niFlag, $goodsOrPsv, $licenceType)
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

    /**
     * Determine whether the licence has changed within set parameters that would
     * qualify this variation to be an interim.
     *
     * @return bool
     */
    protected function hasUpgrade()
    {
        $applicationLicenceTypeId = null;
        if ($this->getLicenceType()) {
            $applicationLicenceTypeId = $this->getLicenceType()->getId();
        }

        $licenceTypeId = null;
        if ($this->getLicence()->getLicenceType()) {
            $licenceTypeId = $this->getLicence()->getLicenceType()->getId();
        }

        // If licence type has been changed from restricted to national or international.
        if (
            $licenceTypeId === Licence::LICENCE_TYPE_RESTRICTED &&
            (
                $applicationLicenceTypeId === Licence::LICENCE_TYPE_STANDARD_NATIONAL ||
                $applicationLicenceTypeId === Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL
            )
        ) {
            return true;
        }

        // If licence is is updated from a standard national to an international.
        if (
            $licenceTypeId === Licence::LICENCE_TYPE_STANDARD_NATIONAL &&
            (
                $applicationLicenceTypeId === Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL
            )
        ) {
            return true;
        }

        return false;
    }

    /**
     * Has the overall number of vehicles authority increased.
     *
     * @return bool
     */
    protected function hasAuthVehiclesIncrease()
    {
        return ((int) $this->getTotAuthVehicles() > (int) $this->getLicence()->getTotAuthVehicles());
    }

    /**
     * Has the overall number of trailers authority increased.
     *
     * @return bool
     */
    protected function hasAuthTrailersIncrease()
    {
        return ($this->getTotAuthTrailers() > $this->getLicence()->getTotAuthTrailers());
    }

    /**
     * Does this variation specify an additional operating centre.
     *
     * @return bool
     */
    protected function hasNewOperatingCentre()
    {
        $operatingCentres = $this->getOperatingCentres();
        /* @var $operatingCentre ApplicationOperatingCentre */
        foreach ($operatingCentres as $operatingCentre) {
            if ($operatingCentre->getAction() === ApplicationOperatingCentre::ACTION_ADD) {
                return true;
            }
        }

        return false;
    }

    /**
     * Does this variation increment an operating centres vehicles or trailers.
     *
     * @return bool
     */
    protected function hasIncreaseInOperatingCentre()
    {
        $licence = array();
        $variation = array();

        // Makes dealing with the records easier.
        /* @var $lOperatingCentre \Dvsa\Olcs\Api\Entity\Licence\LicenceOperatingCentre */
        foreach ($this->getLicence()->getOperatingCentres() as $lOperatingCentre) {
            $licence[$lOperatingCentre->getOperatingCentre()->getId()] = $lOperatingCentre;
        }

        /* @var $aOperatingCentre ApplicationOperatingCentre */
        foreach ($this->getOperatingCentres() as $aOperatingCentre) {
            $variation[$aOperatingCentre->getOperatingCentre()->getId()] = $aOperatingCentre;
        }

        // foreach of the licence op centres.
        foreach (array_keys($licence) as $operatingCenterId) {
            // If a variation record doesnt exists or its a removal op centre.
            if (!isset($variation[$operatingCenterId]) ||
                $variation[$operatingCenterId]->getAction() ===  ApplicationOperatingCentre::ACTION_DELETE
            ) {
                continue;
            }

            if (
                ($variation[$operatingCenterId]->getNoOfVehiclesRequired() >
                    $licence[$operatingCenterId]->getNoOfVehiclesRequired()) ||
                ($variation[$operatingCenterId]->getNoOfTrailersRequired() >
                    $licence[$operatingCenterId]->getNoOfTrailersRequired())
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Can this variation create an interim licence application?
     *
     * @return boolean
     */
    public function canHaveInterimLicence()
    {
        // only goods can have an interim
        if ($this->getGoodsOrPsv()->getId() !== Licence::LICENCE_CATEGORY_GOODS_VEHICLE) {
            return false;
        }

        // if its an application then it can have an interim
        if (!$this->getIsVariation()) {
            return true;
        }

        if ($this->hasAuthVehiclesIncrease() ||
            $this->hasAuthTrailersIncrease() ||
            $this->hasUpgrade() ||
            $this->hasNewOperatingCentre() ||
            $this->hasIncreaseInOperatingCentre()
        ) {
            return true;
        }

        return false;
    }

    /**
     * If the application involves a licence upgrade
     *
     * @return boolean
     */
    public function isLicenceUpgrade()
    {
        // only a variation can be an upgrade
        if (!$this->getIsVariation()) {
            return false;
        }

        $restrictedUpgrades = [
            Licence::LICENCE_TYPE_STANDARD_NATIONAL,
            Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL
        ];

        return (
            $this->getLicence()->getLicenceType()->getId() === Licence::LICENCE_TYPE_RESTRICTED
            && in_array($this->getLicenceType()->getId(), $restrictedUpgrades)
        );
    }
}
