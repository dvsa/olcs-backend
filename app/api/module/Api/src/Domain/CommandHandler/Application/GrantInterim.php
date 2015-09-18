<?php

/**
 * Grant Interim
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Api\Domain\Command\Application\InForceInterim as InForceInterimCmd;
use Dvsa\Olcs\Api\Domain\Command\Document\GenerateAndStore;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity\Fee\Fee;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Transfer\Command\Application\GrantInterim as Cmd;

/**
 * Grant Interim
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class GrantInterim extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Application';

    protected $extraRepos = ['Fee'];

    /**
     * @param Cmd $command
     */
    public function handleCommand(CommandInterface $command)
    {
        /** @var ApplicationEntity $application */
        $application = $this->getRepo()->fetchUsingId($command);

        $existingFees = $this->getExistingFees($application);

        if (count($existingFees)) {

            $this->generateInterimFeeRequestDocument($application, $existingFees[0]);

            $application->setInterimStatus(
                $this->getRepo()->getRefdataReference(ApplicationEntity::INTERIM_STATUS_GRANTED)
            );

            $this->result->addMessage('Interim status updated');

            $this->getRepo()->save($application);

            $this->result->addId('action', 'fee_request');
        } else {
            $this->result->merge($this->handleSideEffect(InForceInterimCmd::create(['id' => $application->getId()])));
            $this->result->addId('action', 'in_force');
        }

        return $this->result;
    }

    /**
     * Get existing grant fees
     *
     * @param ApplicationEntity $application
     * @return array
     */
    private function getExistingFees(ApplicationEntity $application)
    {
        return $this->getRepo('Fee')->fetchInterimFeesByApplicationId($application->getId(), true);
    }

    private function generateInterimFeeRequestDocument(ApplicationEntity $application, Fee $fee)
    {
        if ($application->isVariation()) {
            $description = 'GV Interim direction fee request';
        } else {
            $description = 'GV Interim licence fee request';
        }

        $this->result->merge($this->generateDocument($application, $fee, $description));
    }

    protected function generateDocument(ApplicationEntity $application, Fee $fee, $description)
    {
        $dtoData = [
            'template' => 'FEE_REQ_INT_APP',
            'query' => [
                'application' => $application->getId(),
                'licence' => $application->getLicence()->getId(),
                'fee' => $fee->getId()
            ],
            'description' => $description,
            'application' => $application->getId(),
            'licence' => $application->getLicence()->getId(),
            'category' => Category::CATEGORY_LICENSING,
            'subCategory' => Category::DOC_SUB_CATEGORY_OTHER_DOCUMENTS,
            'isExternal' => false,
            'isScan' => false,
            'dispatch' => true
        ];

        return $this->handleSideEffect(GenerateAndStore::create($dtoData));
    }
}
