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
use Dvsa\Olcs\Api\Domain\Command\Application\CreateApplicationFee as CreateApplicationFeeCmd;
use Dvsa\Olcs\Api\Entity\Fee\FeeType;

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
        $payedFees = $this->getPayedFees($application);

        // if we had payed fees before interim status can be set to in-force
        if (count($payedFees)) {
            $this->result->merge($this->handleSideEffect(InForceInterimCmd::create(['id' => $application->getId()])));
            $this->result->addId('action', 'in_force');
            return $this->result;
        }

        // if there is no fees - we need to create one
        if (!count($existingFees)) {
            $data = [
                'id' => $application->getId(),
                'feeTypeFeeType' => FeeType::FEE_TYPE_GRANTINT
            ];
            $feeResult = $this->handleSideEffect(CreateApplicationFeeCmd::create($data));
            $this->result->merge($feeResult);
            $latestFee = $this->getRepo('Fee')->fetchById($feeResult->getId('fee'));
        } else {
            $latestFee = $existingFees[0];
        }

        // prepare fee request document and change status to granted
        $this->generateInterimFeeRequestDocument($application, $latestFee);
        $application->setInterimStatus(
            $this->getRepo()->getRefdataReference(ApplicationEntity::INTERIM_STATUS_GRANTED)
        );
        $this->result->addMessage('Interim status updated');
        $this->getRepo()->save($application);
        $this->result->addId('action', 'fee_request');

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

    /**
     * Get payed grant fees
     *
     * @param ApplicationEntity $application
     * @return array
     */
    private function getPayedFees(ApplicationEntity $application)
    {
        return $this->getRepo('Fee')->fetchInterimFeesByApplicationId($application->getId(), false, true);
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
