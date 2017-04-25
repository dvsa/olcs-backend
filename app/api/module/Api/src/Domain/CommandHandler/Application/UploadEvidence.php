<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity;

/**
 * UploadEvidence
 */
final class UploadEvidence extends AbstractCommandHandler
{
    protected $repoServiceName = 'Application';

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

        return $this->result;
    }
}
