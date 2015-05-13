<?php

namespace Dvsa\Olcs\Api\Entity\Publication;

use Doctrine\ORM\Mapping as ORM;

/**
 * PublicationSection Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="publication_section",
 *    indexes={
 *        @ORM\Index(name="ix_publication_section_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_publication_section_last_modified_by", columns={"last_modified_by"})
 *    }
 * )
 */
class PublicationSection extends AbstractPublicationSection
{

}
