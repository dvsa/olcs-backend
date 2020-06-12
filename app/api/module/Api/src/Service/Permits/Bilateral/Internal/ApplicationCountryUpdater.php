<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal;

use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;

class ApplicationCountryUpdater
{
    /** @var IrhpPermitApplicationCreator */
    private $irhpPermitApplicationCreator;

    /** @var ExistingIrhpPermitApplicationHandler */
    private $existingIrhpPermitApplicationHandler;

    /**
     * Create service instance
     *
     * @param IrhpPermitApplicationCreator $irhpPermitApplicationCreator
     * @param ExistingIrhpPermitApplicationHandler $existingIrhpPermitApplicationHandler
     *
     * @return ApplicationCountryUpdater
     */
    public function __construct(
        IrhpPermitApplicationCreator $irhpPermitApplicationCreator,
        ExistingIrhpPermitApplicationHandler $existingIrhpPermitApplicationHandler
    ) {
        $this->irhpPermitApplicationCreator = $irhpPermitApplicationCreator;
        $this->existingIrhpPermitApplicationHandler = $existingIrhpPermitApplicationHandler;
    }

    /**
     * Update the application using the supplied country code and application data
     *
     * @param IrhpApplication $irhpApplication
     * @param string $countryId
     * @param int $stockId
     * @param array $requiredPermits
     */
    public function update(IrhpApplication $irhpApplication, $countryId, $stockId, array $requiredPermits)
    {
        $irhpPermitApplication = $irhpApplication->getIrhpPermitApplicationByStockCountryId($countryId);

        if (is_null($irhpPermitApplication)) {
            $this->irhpPermitApplicationCreator->create($irhpApplication, $stockId, $requiredPermits);
        } else {
            $this->existingIrhpPermitApplicationHandler->handle($irhpPermitApplication, $stockId, $requiredPermits);
        }
    }
}
