<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EbsrSubmissionType Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="ebsr_submission_type")
 */
class EbsrSubmissionType implements Interfaces\EntityInterface
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
     * Description
     *
     * @var string
     *
     * @ORM\Column(type="string", name="description", length=45, nullable=true)
     */
    protected $description;
}
