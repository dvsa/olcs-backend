<?php

namespace Dvsa\Olcs\Api\Entity\Publication;

use Doctrine\ORM\Mapping as ORM;

/**
 * Recipient Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="recipient",
 *    indexes={
 *        @ORM\Index(name="ix_recipient_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_recipient_last_modified_by", columns={"last_modified_by"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_recipient_olbs_key", columns={"olbs_key"})
 *    }
 * )
 */
class Recipient extends AbstractRecipient
{

}
