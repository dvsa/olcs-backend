<?php

namespace Dvsa\Olcs\Api\Entity\Organisation;

use Doctrine\ORM\Mapping as ORM;

/**
 * CorrespondenceInbox Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="correspondence_inbox",
 *    indexes={
 *        @ORM\Index(name="ix_correspondence_inbox_document_id", columns={"document_id"}),
 *        @ORM\Index(name="ix_correspondence_inbox_licence_id", columns={"licence_id"}),
 *        @ORM\Index(name="ix_correspondence_inbox_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_correspondence_inbox_last_modified_by", columns={"last_modified_by"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_correspondence_inbox_olbs_key", columns={"olbs_key"})
 *    }
 * )
 */
class CorrespondenceInbox extends AbstractCorrespondenceInbox
{

}
