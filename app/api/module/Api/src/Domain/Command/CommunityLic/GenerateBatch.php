<?php

/**
 * Community Licence / Generate Batch
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Command\CommunityLic;

use Dvsa\Olcs\Transfer\Command\AbstractCommand;

/**
 * Community Licence / Generate Batch
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
final class GenerateBatch extends AbstractCommand
{
    public $licence;

    public $communityLicenceIds;

    public $identifier;

    public function getLicence()
    {
        return $this->licence;
    }

    public function getCommunityLicenceIds()
    {
        return $this->communityLicenceIds;
    }

    public function getIdentifier()
    {
        return $this->identifier;
    }
}
