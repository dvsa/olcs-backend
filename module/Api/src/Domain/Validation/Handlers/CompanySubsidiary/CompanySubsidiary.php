<?php

/**
 * Company Subsidiary
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Validation\Handlers\CompanySubsidiary;

use Dvsa\Olcs\Api\Domain\Validation\Handlers\AbstractHandler;
use Dvsa\Olcs\Api\Entity\Organisation\CompanySubsidiary as CompanySubsidiaryEntity;
use Dvsa\Olcs\Api\Entity\Application\Application;

/**
 * Company Subsidiary
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CompanySubsidiary extends AbstractHandler
{
    /**
     * @inheritdoc
     */
    public function isValid($dto)
    {
        $applicationId = $dto->getApplication();
        $licenceId = $dto->getLicence();

        // If we have no context
        if ($applicationId === null && $licenceId === null) {

            // For internal users, that's ok
            // For non internal users, that's NOT ok
            return $this->isInternalUser();
        }

        // From now on we always need this entity, so may as well get it now
        /** @var CompanySubsidiaryEntity $companySubsidiary */
        $companySubsidiary = $this->getRepo('CompanySubsidiary')->fetchUsingId($dto);

        if ($this->hasNoAccessToRecord($companySubsidiary)) {
            return false;
        }

        return $this->hasContextualAccess($licenceId, $applicationId, $companySubsidiary);
    }

    /**
     * If we are NOT an internal user AND we do NOT own the record, we have no access
     *
     * @param CompanySubsidiaryEntity $companySubsidiary
     * @return bool
     */
    protected function hasNoAccessToRecord(CompanySubsidiaryEntity $companySubsidiary)
    {
        return $this->isInternalUser() === false
            && $this->isOwner($companySubsidiary, $this->getCurrentUser()) === false;
    }

    /**
     * Ensure the company subsidiary record belongs to the application/licence
     *
     * @param $licenceId
     * @param $applicationId
     * @param CompanySubsidiaryEntity $companySubsidiary
     * @return bool
     */
    protected function hasContextualAccess($licenceId, $applicationId, CompanySubsidiaryEntity $companySubsidiary)
    {
        if ($licenceId === null) {
            /** @var Application $application */
            $application = $this->getRepo('Application')->fetchById($applicationId);
            $licence = $application->getLicence();
        } else {
            $licence = $this->getRepo('Licence')->fetchById($licenceId);
        }

        return $licence === $companySubsidiary->getLicence();
    }
}
