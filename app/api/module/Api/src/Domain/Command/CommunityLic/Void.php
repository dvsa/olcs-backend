<?php

/**
 * Void.php
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Command\CommunityLic;

use Dvsa\Olcs\Transfer\Util\Annotation as Transfer;
use Dvsa\Olcs\Transfer\Command\AbstractCommand;

/**
 * Class Void
 *
 * Void community licences.
 *
 * @package Dvsa\Olcs\Transfer\Command\CommunityLic
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
final class Void extends AbstractCommand
{
    public $licence;

    public $communityLicenceIds;

    public $checkOfficeCopy = false;

    public function getLicence()
    {
        return $this->licence;
    }

    public function getCommunityLicenceIds()
    {
        return $this->communityLicenceIds;
    }

    public function getCheckOfficeCopy()
    {
        return $this->checkOfficeCopy;
    }
}
