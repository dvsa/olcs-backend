<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * TransportManagerLicence Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedDate", timeAware=true)
 * @ORM\Table(name="transport_manager_licence",
 *    indexes={
 *        @ORM\Index(name="fk_transport_manager_licence_transport_manager1_idx", 
 *            columns={"transport_manager_id"}),
 *        @ORM\Index(name="fk_transport_manager_licence_licence1_idx", 
 *            columns={"licence_id"}),
 *        @ORM\Index(name="fk_transport_manager_licence_user1_idx", 
 *            columns={"created_by"}),
 *        @ORM\Index(name="fk_transport_manager_licence_user2_idx", 
 *            columns={"last_modified_by"})
 *    }
 * )
 */
class TransportManagerLicence implements Interfaces\EntityInterface
{

    /**
     * Identifier - Id
     *
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * Last modified by
     *
     * @var \Olcs\Db\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\User", fetch="LAZY")
     * @ORM\JoinColumn(name="last_modified_by", referencedColumnName="id", nullable=true)
     */
    protected $lastModifiedBy;

    /**
     * Created by
     *
     * @var \Olcs\Db\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\User", fetch="LAZY")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id", nullable=true)
     */
    protected $createdBy;

    /**
     * Transport manager
     *
     * @var \Olcs\Db\Entity\TransportManager
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\TransportManager", fetch="LAZY")
     * @ORM\JoinColumn(name="transport_manager_id", referencedColumnName="id", nullable=false)
     */
    protected $transportManager;

    /**
     * Licence
     *
     * @var \Olcs\Db\Entity\Licence
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Licence", fetch="LAZY")
     * @ORM\JoinColumn(name="licence_id", referencedColumnName="id", nullable=true)
     */
    protected $licence;

    /**
     * Deleted date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="deleted_date", nullable=true)
     */
    protected $deletedDate;

    /**
     * Created on
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="created_on", nullable=true)
     */
    protected $createdOn;

    /**
     * Last modified on
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="last_modified_on", nullable=true)
     */
    protected $lastModifiedOn;

    /**
     * Version
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="version", nullable=false)
     * @ORM\Version
     */
    protected $version;
}
