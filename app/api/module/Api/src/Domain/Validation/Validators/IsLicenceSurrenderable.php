<?php

namespace Dvsa\Olcs\Api\Domain\Validation\Validators;


use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Domain\LicenceStatusAwareTrait;
use Dvsa\Olcs\Api\Domain\RepositoryManagerAwareInterface;
use Dvsa\Olcs\Api\Domain\RepositoryManagerAwareTrait;
use Dvsa\Olcs\Api\Entity\Licence\Licence;

class IsLicenceSurrenderable extends AbstractValidator implements AuthAwareInterface, RepositoryManagerAwareInterface
{
    use RepositoryManagerAwareTrait;
    use LicenceStatusAwareTrait;
    use AuthAwareTrait;

    public function isValid($licenceId)
    {
        /** @var Licence $licence */
        $licence = $this->getRepo('Licence')->fetchById($licenceId);

        if (!$this->isLicenceStatusSurrenderable($licence)) {
            $status = $licence->getStatus()->getDescription();
            throw new ForbiddenException('This licence cannot be surrendered because it\'s status is: ' . $status);
        }

        $existingSurrender = $this->getRepo('Surrender')->fetchByLicenceId($licenceId);

        if (count($existingSurrender) > 0) {
            throw new ForbiddenException('A surrender record already exists for this licence');
        }

        $openApplications = $this->getRepo('Application')->fetchOpenApplicationsForLicence($licenceId);

        if (count($openApplications) > 0) {
            throw new ForbiddenException('This licence cannot be surrendered because it has open applications');
        }

        return true;

    }
}