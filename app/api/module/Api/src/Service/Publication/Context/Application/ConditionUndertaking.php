<?php

namespace Dvsa\Olcs\Api\Service\Publication\Context\Application;

use Dvsa\Olcs\Api\Service\Publication\Context\AbstractContext;
use Dvsa\Olcs\Api\Entity\Publication\PublicationLink;
use Dvsa\Olcs\Api\Service\Helper\AddressFormatterAwareTrait;
use Dvsa\Olcs\Api\Service\Helper\AddressFormatterAwareInterface;

/**
 * Class ConditionUndertaking
 * @package Dvsa\Olcs\Api\Service\Publication\Context\Application
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class ConditionUndertaking extends AbstractContext implements AddressFormatterAwareInterface
{
    const ATTACHED_LIC = 'Attached to Licence.';
    const ATTACHED_OC = 'Attached to Operating Centre: %s';

    const COND_NEW = 'New %s: %s';
    const COND_REMOVE = '%s to be removed: %s';
    const COND_UPDATE = 'Current %s: %s';
    const COND_AMENDED = 'Amended to: %s';

    use AddressFormatterAwareTrait;

    /**
     * @param PublicationLink $publication
     * @param \ArrayObject $context
     * @return \ArrayObject
     */
    public function provide(PublicationLink $publication, \ArrayObject $context)
    {
        $data = [];
        $conditionUndertakings = $publication->getApplication()->getConditionUndertakings();

        if ($conditionUndertakings->count()) {
            $addressFormatter = $this->getAddressFormatter();

            foreach ($conditionUndertakings as $conditionUndertaking) {
                $action = $conditionUndertaking->getAction();

                //work out the action
                switch ($action) {
                    case 'A':
                        $actionString = self::COND_NEW;
                        break;
                    case 'D':
                        $actionString = self::COND_REMOVE;
                        break;
                    case 'U':
                        $actionString = self::COND_UPDATE;
                        break;
                    default:
                        $actionString = self::COND_NEW;
                }

                $string = sprintf(
                    $actionString,
                    $conditionUndertaking->getConditionType()->getDescription(),
                    $conditionUndertaking->getNotes()
                );

                $operatingCentre = $conditionUndertaking->getOperatingCentre();

                //work out if it's a licence or an oc
                if (!empty($operatingCentre)) {
                    $string = ' Attached to Operating Centre: ' .
                        $addressFormatter->format($operatingCentre->getAddress());
                } else {
                    $string .= ' Attached to Licence.';
                }

                if ($action === 'U') {
                    $string .= " " . sprintf(self::COND_AMENDED, $conditionUndertaking->getNotes());
                }

                $data[] = $string;
            }
        }

        $context->offsetSet('conditionUndertaking', $data);

        return $context;
    }
}
