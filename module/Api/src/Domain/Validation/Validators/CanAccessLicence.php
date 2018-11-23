<?php

/**
 * Can Access Licence
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Validation\Validators;
use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\LicenceStatusAwareTrait;
use Dvsa\Olcs\Api\Domain\RepositoryManagerAwareInterface;
use Dvsa\Olcs\Api\Domain\RepositoryManagerAwareTrait;
use Dvsa\Olcs\Api\Entity\Licence\Licence;

/**
 * Can Access Licence
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CanAccessLicence extends AbstractValidator implements AuthAwareInterface, RepositoryManagerAwareInterface
{
    use RepositoryManagerAwareTrait;
    use LicenceStatusAwareTrait;
    use AuthAwareTrait;


    public function isValid($licenceId)
    {
        if ($this->isInternalUser()) {
            return true;
        }

        if ($this->isSystemUser()) {
            return true;
        }

        /** @var Licence $licence */
        $licence = $this->getRepo('Licence')->fetchById($licenceId);

        if (!$this->isLicenceStatusAccessibleForExternalUser($licence)) {
            return false;
        }

        return $this->isOwner($licence);

    }

    /**
     * Get Licence entity
     *
     * @param mixed $entityId Licence ID or licNo
     *
     * @return \Dvsa\Olcs\Api\Entity\Licence\Licence
     */
    protected function getEntity($entityId)
    {
        if (is_numeric($entityId)) {
            return parent::getEntity($entityId);
        }

        return $this->getRepo($this->repo)->fetchByLicNo($entityId);
    }
}
