<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * IrfoPartner Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="irfo_partner",
 *    indexes={
 *        @ORM\Index(name="fk_irfo_partner_organisation1_idx", 
 *            columns={"organisation_id"}),
 *        @ORM\Index(name="fk_irfo_partner_irfo_psv_auth1_idx", 
 *            columns={"irfo_psv_auth_id"}),
 *        @ORM\Index(name="fk_irfo_partner_user1_idx", 
 *            columns={"created_by"}),
 *        @ORM\Index(name="fk_irfo_partner_user2_idx", 
 *            columns={"last_modified_by"})
 *    }
 * )
 */
class IrfoPartner implements Interfaces\EntityInterface
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
     * Organisation
     *
     * @var \Olcs\Db\Entity\Organisation
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Organisation", fetch="LAZY")
     * @ORM\JoinColumn(name="organisation_id", referencedColumnName="id", nullable=false)
     */
    protected $organisation;

    /**
     * Irfo psv auth
     *
     * @var \Olcs\Db\Entity\IrfoPsvAuth
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\IrfoPsvAuth", fetch="LAZY")
     * @ORM\JoinColumn(name="irfo_psv_auth_id", referencedColumnName="id", nullable=true)
     */
    protected $irfoPsvAuth;

    /**
     * Name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="name", length=70, nullable=false)
     */
    protected $name;

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
