<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Declaration confirmation field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait DeclarationConfirmationField
{
    /**
     * Declaration confirmation
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="declaration_confirmation", nullable=false, options={"default": 0})
     */
    protected $declarationConfirmation = 0;

    /**
     * Set the declaration confirmation
     *
     * @param string $declarationConfirmation
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setDeclarationConfirmation($declarationConfirmation)
    {
        $this->declarationConfirmation = $declarationConfirmation;

        return $this;
    }

    /**
     * Get the declaration confirmation
     *
     * @return string
     */
    public function getDeclarationConfirmation()
    {
        return $this->declarationConfirmation;
    }
}
