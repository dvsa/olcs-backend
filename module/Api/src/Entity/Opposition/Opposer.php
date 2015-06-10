<?php

namespace Dvsa\Olcs\Api\Entity\Opposition;

use Doctrine\Instantiator\Exception\InvalidArgumentException;
use Doctrine\ORM\Mapping as ORM;
use OlcsEntities\Entity\ContactDetails;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Entity\Opposition\Opposition;

/**
 * Opposer Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="opposer",
 *    indexes={
 *        @ORM\Index(name="ix_opposer_contact_details_id", columns={"contact_details_id"}),
 *        @ORM\Index(name="ix_opposer_opposer_type", columns={"opposer_type"}),
 *        @ORM\Index(name="ix_opposer_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_opposer_last_modified_by", columns={"last_modified_by"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_opposer_olbs_key_olbs_type", columns={"olbs_key","olbs_type"})
 *    }
 * )
 */
class Opposer extends AbstractOpposer
{
    public function __construct(
        ContactDetails $contactDetails,
        RefData $opposerType,
        RefData $oppositionType = null
    )
    {
        parent::__construct();
        $this->setContactDetails($contactDetails);

        if (!is_null($oppositionType) &&
            $oppositionType->getId() === Opposition::OPPOSITION_TYPE_ENV &&
            empty($this->getOpposerType())
        ) {
            throw new InvalidArgumentException('Environmental objections must specify an opposition type');
        }
        $this->setOpposerType($opposerType);
    }
}
