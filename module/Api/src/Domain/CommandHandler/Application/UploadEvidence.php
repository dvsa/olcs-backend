<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;

/**
 * UploadEvidence
 */
final class UploadEvidence extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Application';

    protected $extraRepos = ['ApplicationOperatingCentre'];

    /**
     * Handle command
     *
     * @param CommandInterface $command Command DTO
     *
     * @return \Dvsa\Olcs\Api\Domain\Command\Result
     */
    public function handleCommand(CommandInterface $command)
    {
        /** @var Application $application */
        $application = $this->getRepo()->fetchById($command->getId());

        $financialEvidenceDocuments = $application->getApplicationDocuments(
            $this->getRepo()->getCategoryReference(Entity\System\Category::CATEGORY_APPLICATION),
            $this->getRepo()->getSubCategoryReference(
                Entity\System\SubCategory::DOC_SUB_CATEGORY_FINANCIAL_EVIDENCE_DIGITAL
            )
        );

        if (!$financialEvidenceDocuments->isEmpty()) {
            $application->setFinancialEvidenceUploaded(Application::FINANCIAL_EVIDENCE_UPLOADED);
            $this->getRepo()->save($application);
            $this->result->addMessage('Financial evidence uploaded');
        }

        foreach ($command->getOperatingCentres() as $commandOc) {
            $applicationOperatingCentre = $application->getApplicationOperatingCentreById($commandOc['aocId']);
            if ($applicationOperatingCentre === null) {
                throw new ValidationException(['No operating centre found']);
            }
            $operatingCentreId = $applicationOperatingCentre->getOperatingCentre()->getId();
            $advertDigitalDocuments = $application->getApplicationDocuments(
                $this->getRepo()->getCategoryReference(Entity\System\Category::CATEGORY_APPLICATION),
                $this->getRepo()->getSubCategoryReference(
                    Entity\System\SubCategory::DOC_SUB_CATEGORY_ADVERT_DIGITAL
                ),
                $this->getRepo()->getReference(Entity\OperatingCentre\OperatingCentre::class, $operatingCentreId)
            );
            if (
                !$advertDigitalDocuments->isEmpty()
                && !empty($commandOc['adPlacedIn'])
                && !empty($commandOc['adPlacedDate'])
            ) {
                $applicationOperatingCentre->setAdPlaced(Entity\Application\ApplicationOperatingCentre::AD_UPLOAD_NOW);
                $this->result->addMessage('Advert digital documents for OC ' . $operatingCentreId . ' uploaded');
            }

            $applicationOperatingCentre->setAdPlacedIn($commandOc['adPlacedIn']);
            $applicationOperatingCentre->setAdPlacedDate($commandOc['adPlacedDate']);
            $this->getRepo('ApplicationOperatingCentre')->save($applicationOperatingCentre);
            $this->result->addMessage('Advert details for OC ' . $operatingCentreId . ' saved');
        }

        return $this->result;
    }
}
