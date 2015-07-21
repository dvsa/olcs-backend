<?php

/**
 * Grant Interim
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\Command\Application\InForceInterim as InForceInterimCmd;
use Dvsa\Olcs\Api\Domain\Command\Document\DispatchDocument;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\DocumentGeneratorAwareInterface as DocGenAwareInterface;
use Dvsa\Olcs\Api\Domain\DocumentGeneratorAwareTrait;
use Dvsa\Olcs\Api\Entity\Fee\Fee;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Transfer\Command\Application\GrantInterim as Cmd;
use Dvsa\Olcs\Transfer\Query\Application\Application;

/**
 * Grant Interim
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class GrantInterim extends AbstractCommandHandler implements
    TransactionedInterface,
    DocGenAwareInterface,
    AuthAwareInterface
{
    use DocumentGeneratorAwareTrait,
        AuthAwareTrait;

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

        $query = [
            'application' => $application->getId(),
            'licence' => $application->getLicence()->getId(),
            'fee' => $fee->getId(),
            'user' => $this->getUser()->getId()
        ];

        $storedFile = $this->getDocumentGenerator()->generateAndStore('FEE_REQ_INT_APP', $query);
        $this->result->addMessage('Document generated');

        $data = [
            'identifier' => $storedFile->getIdentifier(),
            'size' => $storedFile->getSize(),
            'description' => $description,
            'filename' => str_replace(' ', '_', $description) . '.rtf',
            'application' => $application->getId(),
            'licence' => $application->getLicence()->getId(),
            'category' => Category::CATEGORY_LICENSING,
            'subCategory' => Category::DOC_SUB_CATEGORY_OTHER_DOCUMENTS,
            'isExternal' => false,
            'isScan' => false
        ];

        $this->result->merge($this->handleSideEffect(DispatchDocument::create($data)));
    }
}
