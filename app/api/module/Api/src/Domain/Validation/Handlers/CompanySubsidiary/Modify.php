<?php

/**
 * Modify
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Validation\Handlers\CompanySubsidiary;

use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\RepositoryManagerAwareInterface;
use Dvsa\Olcs\Api\Domain\RepositoryManagerAwareTrait;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\AbstractHandler;
use Dvsa\Olcs\Api\Entity\Application\Application;

/**
 * Modify
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Modify extends AbstractHandler implements AuthAwareInterface, RepositoryManagerAwareInterface
{
    use AuthAwareTrait,
        RepositoryManagerAwareTrait;

    /**
     * @inheritdoc
     */
    public function isValid($dto)
    {
        // If we have no context
        if (!$this->hasContext($dto)) {

            // For internal users, that's ok
            // For non internal users, that's NOT ok
            return $this->isInternalUser();
        }

        $ids = $this->getIds($dto);

        $isInternal = $this->isInternalUser();

        foreach ($ids as $id) {
            // If the user has no access to 1 or more records, bail
            if ($this->hasNoAccessToRecord($id, $isInternal)) {
                return false;
            }
        }

        return $this->hasContextualAccess($dto, $ids);
    }

    /**
     * If we are NOT an internal user AND we do NOT own the record, we have no access
     *
     * @param $companySubsidiaryId
     * @return bool
     */
    protected function hasNoAccessToRecord($companySubsidiaryId, $isInternal)
    {
        return $isInternal === false && $this->doesOwnCompanySubsidiary($companySubsidiaryId) === false;
    }

    /**
     * Ensure the company subsidiary record belongs to the application/licence
     *
     * @param $licenceId
     * @param $applicationId
     * @param $companySubsidiary
     * @return bool
     */
    protected function hasContextualAccess($dto, $ids)
    {
        $companySubsidiaries = $this->getRepo('CompanySubsidiary')->fetchByIds($ids);

        $licence = $this->getLicence($dto);

        foreach ($companySubsidiaries as $companySubsidiary) {
            // If 1 or more records doesn't link to the licence
            if ($licence !== $companySubsidiary->getLicence()) {
                return false;
            }
        }

        return true;
    }

    protected function getIds($dto)
    {
        return [$dto->getId()];
    }

    protected function getLicence($dto)
    {
        if ($dto->getLicence() === null) {
            /** @var Application $application */
            $application = $this->getRepo('Application')->fetchById($dto->getApplication());
            return $application->getLicence();
        }

        return $this->getRepo('Licence')->fetchById($dto->getLicence());
    }

    protected function hasContext($dto)
    {
        $applicationId = $dto->getApplication();
        $licenceId = $dto->getLicence();

        return ($applicationId !== null || $licenceId !== null);
    }
}
