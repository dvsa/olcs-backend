<?php

/**
 * Community Licence / Generate Batch
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Command\CommunityLic;

use Dvsa\Olcs\Transfer\Command\AbstractCommand;
use Dvsa\Olcs\Transfer\FieldType\Traits\UserOptional;

/**
 * Community Licence / Generate Batch
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
final class GenerateBatch extends AbstractCommand
{
    use UserOptional;

    public $isReprint;

    public $licence;

    public $communityLicenceIds;

    public $identifier;

    /**
     * @return bool
     */
    public function getIsReprint()
    {
        return $this->isReprint;
    }

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
