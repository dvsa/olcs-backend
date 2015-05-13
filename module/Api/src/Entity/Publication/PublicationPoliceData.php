<?php

namespace Dvsa\Olcs\Api\Entity\Publication;

use Doctrine\ORM\Mapping as ORM;

/**
 * PublicationPoliceData Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="publication_police_data",
 *    indexes={
 *        @ORM\Index(name="ix_publication_police_data_publication_link_id", columns={"publication_link_id"}),
 *        @ORM\Index(name="ix_publication_police_data_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_publication_police_data_last_modified_by", columns={"last_modified_by"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_publication_police_data_olbs_key", columns={"olbs_key"})
 *    }
 * )
 */
class PublicationPoliceData extends AbstractPublicationPoliceData
{

}
