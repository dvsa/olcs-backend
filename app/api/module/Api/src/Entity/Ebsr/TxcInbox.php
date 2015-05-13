<?php

namespace Dvsa\Olcs\Api\Entity\Ebsr;

use Doctrine\ORM\Mapping as ORM;

/**
 * TxcInbox Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="txc_inbox",
 *    indexes={
 *        @ORM\Index(name="ix_txc_inbox_bus_reg_id", columns={"bus_reg_id"}),
 *        @ORM\Index(name="ix_txc_inbox_local_authority_id", columns={"local_authority_id"}),
 *        @ORM\Index(name="ix_txc_inbox_organisation_id", columns={"organisation_id"}),
 *        @ORM\Index(name="ix_txc_inbox_zip_document_id", columns={"zip_document_id"}),
 *        @ORM\Index(name="ix_txc_inbox_route_document_id", columns={"route_document_id"}),
 *        @ORM\Index(name="ix_txc_inbox_pdf_document_id", columns={"pdf_document_id"}),
 *        @ORM\Index(name="ix_txc_inbox_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_txc_inbox_last_modified_by", columns={"last_modified_by"})
 *    }
 * )
 */
class TxcInbox extends AbstractTxcInbox
{

}
