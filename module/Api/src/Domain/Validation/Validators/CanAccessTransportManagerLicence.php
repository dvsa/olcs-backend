<?php

namespace Dvsa\Olcs\Api\Domain\Validation\Validators;

/**
 * Can Access TransportManagerLicence
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class CanAccessTransportManagerLicence extends AbstractCanAccessEntity
{
    protected $repo = 'TransportManagerLicence';

    /**
     * Can current user access a TransportManagerApplication
     *
     * @param int $entityId TransportManagerApplication ID
     *
     * @return boolean
     */
    public function isValid($entityId)
    {
        /* @var $tma \Dvsa\Olcs\Api\Entity\Tm\TransportManagerLicence */
        $tml = $this->getRepo($this->repo)->fetchById($entityId);
        if ($tml->getTransportManager() === $this->getCurrentUser()->getTransportManager()) {
            return true;
        }

        return parent::isValid($entityId);
    }
}
