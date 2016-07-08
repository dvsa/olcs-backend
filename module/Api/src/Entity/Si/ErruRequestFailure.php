<?php

namespace Dvsa\Olcs\Api\Entity\Si;

use Doctrine\ORM\Mapping as ORM;

/**
 * ErruRequestFailure Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="erru_request_failure",
 *    indexes={
 *        @ORM\Index(name="ix_erru_request_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_erru_request_last_modified_by", columns={"last_modified_by"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_erru_request_failure_document_id", columns={"document_id"})
 *    }
 * )
 */
class ErruRequestFailure extends AbstractErruRequestFailure
{

}
