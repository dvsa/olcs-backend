<?php

namespace Dvsa\Olcs\Api\Service\Publication\Context\Application;

use Dvsa\Olcs\Api\Service\Publication\Context\AbstractContext;
use Dvsa\Olcs\Api\Entity\Publication\PublicationLink;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Application\ApplicationOperatingCentre as ApplicationOperatingCentreEntity;
use Dvsa\Olcs\Api\Service\Helper\AddressFormatterAwareTrait;
use Dvsa\Olcs\Api\Service\Helper\AddressFormatterAwareInterface;

/**
 * Class Operating Centres
 * @package Dvsa\Olcs\Api\Service\Publication\Context\Application
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
final class OperatingCentres extends AbstractContext implements AddressFormatterAwareInterface
{
    use AddressFormatterAwareTrait;

    /**
     * @param PublicationLink $publication
     * @param \ArrayObject $context
     * @return \ArrayObject
     */
    public function provide(PublicationLink $publication, \ArrayObject $context)
    {
        $newOcData = [];
        $application = $publication->getApplication();
        $licType = $application->getGoodsOrPsv()->getId();
        $operatingCentres = $application->getOperatingCentres();
        $authorisationText = '';
        $addressFormatter = $this->getAddressFormatter();

        foreach ($operatingCentres as $oc) {
            /**
             * @var ApplicationOperatingCentreEntity $oc
             */
            $totAuthTrailers = $oc->getNoOfTrailersRequired();
            $totAuthVehicles = $oc->getNoOfVehiclesRequired();

            $gvWithTrailers = (bool)($licType == LicenceEntity::LICENCE_CATEGORY_GOODS_VEHICLE && $totAuthTrailers);

            //check we have authorised vehicles
            if ($totAuthVehicles || $gvWithTrailers) {
                $authorisationText = 'Authorisation: ';

                if ($totAuthVehicles && $gvWithTrailers) {
                    $authorisationText .= $totAuthVehicles . ' Vehicle(s) and ' . $totAuthTrailers . ' Trailer(s)';
                } elseif ($totAuthVehicles) {
                    $authorisationText .= $totAuthVehicles . ' Vehicle(s)';
                } else {
                    $authorisationText .= $totAuthTrailers . ' Trailer(s)';
                }
            }

            $ocAction = $oc->getAction();

            switch ($ocAction) {
                case 'U':
                    $prefix = 'Update ';
                    break;
                case 'D':
                    $prefix = 'Remove ';
                    break;
                default:
                    $prefix = 'New ';
            }

            $operatingCentreText = $addressFormatter->format($oc->getOperatingCentre()->getAddress());

            $newOcData[] = trim($prefix . 'Operating Centre: ' . $operatingCentreText . ' ' . $authorisationText);
        }

        $context->offsetSet('operatingCentres', $newOcData);

        return $context;
    }
}
